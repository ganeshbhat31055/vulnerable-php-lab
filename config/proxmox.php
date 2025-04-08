<?php

return [
    'api_url' => env('PROXMOX_API_URL', 'https://your-proxmox-server:8006/api2/json/'),
    'username' => env('PROXMOX_USERNAME'),
    'password' => env('PROXMOX_PASSWORD'),
    'node' => env('PROXMOX_NODE', 'proxmox'),
]; 