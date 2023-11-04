<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetAPIEeauWebhook extends Command
{
    protected $signature = 'eau:webhook:set';
    protected $description = 'Set the Telegram eau bot webhook URL';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $this->info('Setting up the Telegram bot webhook...');
            $token = env("TELEGRAM_BOT_EAU_API_TOKEN");
            $telegram = new \Longman\TelegramBot\Telegram($token);
            $webhookUrl = "https://demo.kvolts-lab.com/api/eau/webhook"; // Replace with your actual webhook URL
            $response = $telegram->setWebhook($webhookUrl);
            if ($response->isOk()) {
                $this->info('Webhook URL successfully set!');
                $this->info($response->getDescription());
            }
        } catch (\Longman\TelegramBot\Exception\TelegramException $e) {
            $this->error('Failed to set the webhook URL: ' . $e->getMessage());
        }
    }
}
