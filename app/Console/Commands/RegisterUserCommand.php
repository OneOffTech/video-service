<?php

namespace App\Console\Commands;

use App\Actions\RegisterUser;
use Illuminate\Console\Command;

class RegisterUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'register {email?} {name?} {password?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register a new user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(RegisterUser $registerUser)
    {
        $user = $registerUser($this->data());

        $this->line("User [{$user->email}] created.");

        return Command::SUCCESS;
    }

    private function data(): array
    {
        return [
            'email' => $this->argument('email') ?? $this->ask('The user email address?'),
            'name' => $this->argument('name') ?? $this->ask('What is the user\'s name?'),
            'password' => $this->argument('password') ?? $this->secret('The password to assign to the user?'),
            'password_confirmation' => $this->argument('password') ?? $this->secret('Confirm the password?'),
        ];
    }
}
