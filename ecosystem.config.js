module.exports = {
    apps: [{
      name: 'deploylogics-whatsapp-queue',
      cwd: '/var/www/royalerp.net/deploylogics',
      script: 'artisan',
      interpreter: 'php',
      args: 'queue:work database --queue=whatsapp --sleep=3 --tries=3 --timeout=60',
      autorestart: true,
      max_restarts: 20,
      restart_delay: 5000,
      out_file: './storage/logs/pm2-queue-out.log',
      error_file: './storage/logs/pm2-queue-error.log',
      merge_logs: true,
      time: true,
    }]
  };
