# OAuth Login Implementation

## Overview
This implementation adds OAuth login functionality for GitHub and Google to your GuacPanel application. Users can login using their GitHub or Google accounts, but only existing users are allowed to login. New users will receive a message that they need to be registered by an administrator.

## Setup Instructions

### 1. Environment Configuration

Add the following OAuth credentials to your `.env` file:

```env
# GitHub OAuth
GITHUB_CLIENT_ID=your_github_client_id
GITHUB_CLIENT_SECRET=your_github_client_secret
GITHUB_REDIRECT_URI=${APP_URL}/auth/github/callback

# Google OAuth
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI=${APP_URL}/auth/google/callback
```

### 2. Getting OAuth Credentials

#### GitHub OAuth App
1. Go to GitHub Settings > Developer settings > OAuth Apps
2. Click "New OAuth App"
3. Fill in:
   - Application name: Your app name
   - Homepage URL: Your app URL
   - Authorization callback URL: `http://your-domain.com/auth/github/callback`
4. Save and copy the Client ID and Client Secret

#### Google OAuth Client
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select existing
3. Enable Google+ API
4. Go to Credentials > Create Credentials > OAuth Client ID
5. Select "Web application"
6. Add authorized redirect URI: `http://your-domain.com/auth/google/callback`
7. Save and copy the Client ID and Client Secret

### 3. Features Implemented

- **OAuth Controller** (`app/Http/Controllers/Auth/SocialAuthController.php`)
  - Handles redirect to OAuth providers
  - Processes OAuth callbacks
  - Checks if user exists by email
  - Logs user in if they exist
  - Shows error message for unregistered users

- **Database Migration**
  - Added `social_logins` JSON column to users table
  - Stores provider information (id, email, avatar, etc.)

- **Routes**
  - `/auth/{provider}` - Redirects to OAuth provider
  - `/auth/{provider}/callback` - Handles OAuth callback

- **Frontend Updates**
  - Updated Login.vue with working OAuth buttons
  - Added flash message display for errors/success
  - Added loading state for OAuth buttons

### 4. How It Works

1. User clicks GitHub/Google button on login page
2. User is redirected to OAuth provider for authentication
3. After successful authentication, user is redirected back to your app
4. The app checks if a user with the OAuth email exists:
   - **If user exists**: User is logged in and redirected to dashboard
   - **If user doesn't exist**: User sees error message "No account found with this email. Please contact your administrator to register."

### 5. Security Features

- Provider validation (only allows github/google)
- Error handling for OAuth failures
- Activity logging for OAuth logins
- Session-based authentication
- CSRF protection

### 6. Testing

1. Make sure you have at least one user in your database
2. Try logging in with GitHub/Google using an email that exists in your users table
3. Try logging in with an email that doesn't exist to see the error message

### 7. Troubleshooting

- **Invalid state exception**: Clear browser cookies and try again
- **Redirect URI mismatch**: Make sure the redirect URI in your .env file matches exactly what you configured in GitHub/Google
- **User not found error**: Ensure the email from OAuth provider matches an existing user's email in your database

## Notes

- Users must be pre-registered by an administrator
- The system uses email as the unique identifier to match OAuth accounts with existing users
- Social login information is stored in the `social_logins` JSON column for future reference
- User avatars from OAuth providers are stored if the user doesn't have an avatar set
