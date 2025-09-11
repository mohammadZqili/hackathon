<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TestTeamLeadRoutes extends Command
{
    protected $signature = 'test:teamlead-routes';
    protected $description = 'Test all team leader routes';

    public function handle()
    {
        // Find or create a team leader user
        $user = User::whereHas('roles', function($q) { 
            $q->where('name', 'team_leader'); 
        })->first();

        if (!$user) {
            $user = User::where('email', 'teamlead@test.com')->first();
            if (!$user) {
                $user = User::create([
                    'name' => 'Team Leader Test',
                    'email' => 'teamlead@test.com',
                    'password' => bcrypt('password'),
                    'email_verified_at' => now()
                ]);
                $user->assignRole('team_leader');
                $this->info("Created test user: teamlead@test.com");
            }
        }

        $this->info("Testing routes as: {$user->email}");
        Auth::login($user);

        $routes = [
            ['GET', '/team-leader/dashboard', 'Dashboard'],
            ['GET', '/team-leader/team', 'Team Show'],
            ['GET', '/team-leader/team/edit', 'Team Edit'],
            ['GET', '/team-leader/idea', 'Idea Show'],
            ['GET', '/team-leader/idea/create', 'Idea Create'],
            ['GET', '/team-leader/idea/edit', 'Idea Edit'],
        ];

        foreach ($routes as [$method, $path, $name]) {
            $this->info("\n\nTesting: $name ($path)");
            
            try {
                $request = request()->create($path, $method);
                $request->setUserResolver(function () use ($user) {
                    return $user;
                });
                
                // Get the route
                $route = app('router')->getRoutes()->match($request);
                $controller = $route->getController();
                $methodName = $route->getActionMethod();
                
                $this->info("  Controller: " . get_class($controller));
                $this->info("  Method: $methodName");
                
                // Call the controller method
                $response = $controller->$methodName($request);
                
                if (method_exists($response, 'getStatusCode')) {
                    $this->info("  Status: " . $response->getStatusCode());
                    if ($response->getStatusCode() == 302) {
                        $this->info("  Redirect to: " . $response->headers->get('Location'));
                    }
                } else {
                    $this->info("  Response type: " . get_class($response));
                }
                
                $this->info("  ✓ SUCCESS");
                
            } catch (\Exception $e) {
                $this->error("  ✗ ERROR: " . $e->getMessage());
                $this->error("  File: " . $e->getFile() . ":" . $e->getLine());
            }
        }
        
        return 0;
    }
}