const express = require('express');
let router = express.Router();

let { generateId, normalizeUrl, isExisting } = require("../lib/utils");
let Link = require("../models/Link");

router.get('/:uniqueId', (req, res, next) => {
  if (!req.params.uniqueId) return next();
  Link.findOne({ "uniqueId": req.params.uniqueId })
    .then(async doc => {
      const url = doc.originalLink;
      await Link.findOneAndUpdate({ "_id": doc._id }, { "timesVisited": doc.timesVisited + 1 });
      return doc && url
        ? res.redirect(url)
        : next()
    })
    .catch(e => next())
});

router.get("/", (req, res, next) => {
  let ip = req.headers['X-Forwarded-for'] || req.connection.remoteAddress;
  console.log(ip || req.headers);
  res.render("index", {
    title: "Shorten a new link"
  });
});

router.post("/", async (req, res, next) => {
  let originalString= req.body.link;
  let link = normalizeUrl(originalString());
  let existing = await isExisting(link);

  let uniqueId = existing ? existing.uniqueId : generateId(process.env.UNIQUE_STRINGS_LENGTH);

  let myLink = new Link({
    "originalLink": normalizeUrl(link),
    "uniqueId": uniqueId,
    "baseString": originalString()
  });

  myLink.save()
    .then(doc => {
      res.render("result", {
        shortLink: (`${process.env.HOSTNAME}/${doc.uniqueId}`),
        stylesheet: "result.css",
        script: "listener.js"
      })
    })
    .catch(e => next())
});

module.exports = router;
