// MongoDB initialization script
// This script runs when the MongoDB container starts for the first time

// Switch to the Laravel database
db = db.getSiblingDB('laravel_db');

// Create a user for the Laravel application
db.createUser({
  user: 'laravel_user',
  pwd: 'laravel_password',
  roles: [
    {
      role: 'readWrite',
      db: 'laravel_db'
    }
  ]
});

// Create a sample collection to verify the setup
db.createCollection('users');

print('MongoDB initialization completed for Laravel application');
