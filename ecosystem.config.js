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
      NODE_ENV: 'dev'
    },
    env_production: {
      NODE_ENV: 'prod'
    }
  }],

  deploy : {
    production : {
      user : 'edouard',
      host : 'edouardcourty.fr',
      ref  : 'origin/master',
      repo : 'git@github.com/EdouardCourty/myUrlShortener.git',
      path : '/home/edouard/workspace/myUrlShortener',
      'post-deploy' : 'npm install && pm2 reload ecosystem.config.js --env production'
    }
  }
};
