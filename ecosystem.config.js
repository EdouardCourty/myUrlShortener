module.exports = {
  apps : [
    {
      name: "myUrlShortener",
      script: "bin/www",
      watch: true,
      autorestart: true,
      max_memory_restart: "500M",
      env: {
        "APPLICATION_PORT": 3000,
        "NODE_ENV": "development",
        "UNIQUE_STRINGS_LENGTH": 7,
        "HOSTNAME": "http://localhost:3000"
      },
      env_production: {
        "APPLICATION_PORT": 3001,
        "NODE_ENV": "production",
        "UNIQUE_STRINGS_LENGTH": 7,
        "HOSTNAME": "https://link.edouardcourty.fr"
      }
    }
  ],

  deploy: {
    production: {
      user: "edouard",
      host: "edouardcourty.fr",
      ref: "origin/master",
      path: "/var/www/myUrlShortener",
      repo: "https://github.com/EdouardCourty/myUrlShortener",
      "post-reploy": "npm i && pm2 reload ecosystem.config.js --env production"
    }
  }
};