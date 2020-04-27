const Link = require("../models/Link");

module.exports = {
  generateId: (length = 8) => {
    let id = "";
    let dict = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    for ( let i = 0; i < length; i++ ) {
      id += dict[(Math.floor(Math.random() * dict.length))]
    }
    return id;
  },

  genId: l => {
    const dict = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    return Array.from(Array(l)).map(v => dict[Math.floor(Math.random() * dict.length)]).join("")
  },

  normalizeUrl: url => {
    let newUrl = url.split(/:\/\//).pop();
    return "http://" + newUrl
      .replace(/:\d*/g, "")
      .replace(/ /g, "+")
  },

  isExisting: link => {
    return new Promise((resolve, reject) => {
      Link.findOne({ "originalLink": link })
        .then(resolve)
        .catch(reject)
    })
  }
};

