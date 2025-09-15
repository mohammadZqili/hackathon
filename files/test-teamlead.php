<?php

use Illuminate\Support\Facades\Auth;
use App\Models\User;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Find or create a team leader user
$user = User::whereHas('roles', function($q) { 
    $q->where('name', 'team_leader'); 
})->first();

if (!$user) {
    $user = User::where('email', 'teamlead@example.com')->first();
    if (!$user) {
        $user = User::create([
            'name' => 'Team Leader Test',
            'email' => 'teamlead@example.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now()
        ]);
        $user->assignRole('team_leader');
    }
}

echo "Testing as user: {$user->email}\n\n";

// Test all team leader routes
$routes = [
    'team-leader.dashboard' => 'GET /team-leader/dashboard',
    'team-leader.team.show' => 'GET /team-leader/team',
    'team-leader.team.edit' => 'GET /team-leader/team/edit',
    'team-leader.idea.show' => 'GET /team-leader/idea',
    'team-leader.idea.create' => 'GET /team-leader/idea/create',
    'team-leader.idea.edit' => 'GET /team-leader/idea/edit',
];

// Authenticate as the user
Auth::login($user);

foreach ($routes as $name => $route) {
    [$method, $path] = explode(' ', $route);
    echo "Testing: $name ($route)\n";
    
    $request = Illuminate\Http\Request::create($path, $method);
    $request->setUserResolver(function () use ($user) {
        return $user;
    });
    
    try {
        $response = $kernel->handle($request);
        $content = $response->getContent();
        
        if ($response->getStatusCode() >= 400) {
            echo "  ERROR: Status {$response->getStatusCode()}\n";
            if (strpos($content, 'exception') !== false || strpos($content, 'Error') !== false) {
                preg_match('/"message":"([^"]+)"/', $content, $matches);
                if (isset($matches[1])) {
                    echo "  Message: {$matches[1]}\n";
                }
            }
        } else {
            echo "  SUCCESS: Status {$response->getStatusCode()}\n";
        }
    } catch (\Exception $e) {
        echo "  EXCEPTION: " . $e->getMessage() . "\n";
        echo "  File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    }
    echo "\n";
}