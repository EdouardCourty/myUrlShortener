module.exports = {
  apps : [{
    name: 'UrlShortener',
    script: './bin/www',

    // Options reference: https://pm2.keymetrics.io/docs/usage/application-declaration/
    // args: 'one two',
    instances: 1,
    autorestart: true,
    watch: false,
    max_memory_restart: '500M',
    env: {
      NODE_ENV: 'dev',
      HOSTNAME: "http://localhost:3000",
      APPLICATION_PORT: 3000,
      UNIQUE_STRINGS_LENGTH: 7
    },
    env_production: {
      NODE_ENV: 'prod',
      HOSTNAME: "http://link.edouardcourty.fr",
      APPLICATION_PORT: 3001,
      IS_PRODUCTION: true,
      UNIQUE_STRINGS_LENGTH: 7
    }
  }],

  deploy : {
    production : {
      user : 'edouard',
      host : 'edouardcourty.fr',
      ref  : 'origin/master',
      repo : 'http://github.com/EdouardCourty/myUrlShortener',
      path : '/var/www/link.edouardcourty.fr',
      'post-deploy' : 'npm install && pm2 reload ecosystem.config.js --env production'
    }
  }
};
