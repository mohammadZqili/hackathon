<?php

namespace App\Services;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Support\Facades\Log;

class QrCodeService
{
    /**
     * Generate QR code for workshop registration
     *
     * @param string $userEmail
     * @param int $workshopId
     * @param int|null $registrationId
     * @return string Base64 encoded PNG image
     */
    public function generateWorkshopQrCode(string $userEmail, int $workshopId, ?int $registrationId = null): string
    {
        try {
            // Create QR content with user email and workshop ID
            // Format: WORKSHOP_{id}_USER_{email}_TIME_{timestamp}_REG_{registrationId}
            $timestamp = time();
            $content = sprintf(
                'WORKSHOP_%d_USER_%s_TIME_%d',
                $workshopId,
                base64_encode($userEmail), // Encode email to handle special characters
                $timestamp
            );

            if ($registrationId) {
                $content .= '_REG_' . $registrationId;
            }

            // Generate QR code as base64 PNG
            return $this->generateQrCodeImage($content);
        } catch (\Exception $e) {
            Log::error('Failed to generate QR code', [
                'email' => $userEmail,
                'workshop_id' => $workshopId,
                'error' => $e->getMessage()
            ]);

            // Return a fallback simple QR code
            return $this->generateSimpleQrCode($userEmail . '|' . $workshopId);
        }
    }

    /**
     * Generate a QR code image as base64 encoded PNG
     *
     * @param string $content
     * @param int $size
     * @return string
     */
    public function generateQrCodeImage(string $content, int $size = 300): string
    {
        try {
            // Create QR code with data using v6 constructor syntax
            $qrCode = new QrCode(
                data: $content,
                size: $size,
                margin: 10
            );

            // Create writer and generate the QR code
            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            // Convert to base64 data URI
            return 'data:image/png;base64,' . base64_encode($result->getString());
        } catch (\Exception $e) {
            Log::error('QR code generation failed', [
                'content' => $content,
                'error' => $e->getMessage()
            ]);

            // Fallback to simple text representation
            return $this->generateFallbackQr($content);
        }
    }

    /**
     * Generate a simple QR code for fallback
     *
     * @param string $content
     * @return string
     */
    private function generateSimpleQrCode(string $content): string
    {
        try {
            // Create QR code with data using v6 constructor syntax
            $qrCode = new QrCode(
                data: $content,
                size: 200,
                margin: 5
            );

            // Create writer and generate the QR code
            $writer = new SvgWriter();
            $result = $writer->write($qrCode);

            return 'data:image/svg+xml;base64,' . base64_encode($result->getString());
        } catch (\Exception $e) {
            return $this->generateFallbackQr($content);
        }
    }

    /**
     * Generate fallback QR representation
     *
     * @param string $content
     * @return string
     */
    private function generateFallbackQr(string $content): string
    {
        // Create a simple placeholder SVG with the content as text
        $svg = sprintf('
            <svg xmlns="http://www.w3.org/2000/svg" width="200" height="200">
                <rect width="200" height="200" fill="white"/>
                <rect x="10" y="10" width="180" height="180" fill="white" stroke="black" stroke-width="2"/>
                <text x="100" y="100" text-anchor="middle" font-size="10" fill="black">QR: %s</text>
            </svg>
        ', htmlspecialchars(substr($content, 0, 20)));

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    /**
     * Parse QR code content to extract user email and workshop ID
     *
     * @param string $qrContent
     * @return array|null
     */
    public function parseWorkshopQrCode(string $qrContent): ?array
    {
        // Try JSON format from workshop registration emails
        $jsonData = json_decode($qrContent, true);
        if ($jsonData && isset($jsonData['workshop_id'])) {
            return [
                'workshop_id' => (int) $jsonData['workshop_id'],
                'user_email' => $jsonData['user_email'] ?? null,
                'user_name' => $jsonData['user_name'] ?? null,
                'registration_id' => isset($jsonData['registration_id']) ? (int) $jsonData['registration_id'] : null,
                'timestamp' => isset($jsonData['registered_at']) ? strtotime($jsonData['registered_at']) : time(),
                'format' => 'email_based'
            ];
        }

        // Try new format: WORKSHOP_{id}_USER_{email}_TIME_{timestamp}_REG_{registrationId}
        if (preg_match('/WORKSHOP_(\d+)_USER_([^_]+)_TIME_(\d+)(?:_REG_(\d+))?/', $qrContent, $matches)) {
            return [
                'workshop_id' => (int) $matches[1],
                'user_email' => base64_decode($matches[2]),
                'timestamp' => (int) $matches[3],
                'registration_id' => isset($matches[4]) ? (int) $matches[4] : null,
                'format' => 'email_based'
            ];
        }

        // Try legacy format: WORKSHOP_{workshop_id}_REG_{registration_id}_CODE_{barcode}
        if (preg_match('/WORKSHOP_(\d+)_REG_(\d+)_CODE_(.+)/', $qrContent, $matches)) {
            return [
                'workshop_id' => (int) $matches[1],
                'registration_id' => (int) $matches[2],
                'barcode' => $matches[3],
                'format' => 'legacy'
            ];
        }

        // Try simple format: email|workshop_id
        if (strpos($qrContent, '|') !== false) {
            list($email, $workshopId) = explode('|', $qrContent, 2);
            return [
                'workshop_id' => (int) $workshopId,
                'user_email' => $email,
                'format' => 'simple'
            ];
        }

        return null;
    }

    /**
     * Generate QR code for general check-in
     *
     * @param string $userId
     * @param string $eventType
     * @return string
     */
    public function generateCheckInQrCode(string $userId, string $eventType = 'general'): string
    {
        $content = sprintf(
            'CHECKIN_%s_USER_%s_TIME_%d',
            strtoupper($eventType),
            $userId,
            time()
        );

        return $this->generateQrCodeImage($content);
    }
}