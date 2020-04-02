const express = require('express');
let router = express.Router();

let { generateId, normalizeUrl } = require("../lib/utils");
let Link = require("../models/Link");

router.get('/:uniqueId', (req, res, next) => {
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
  res.render("index", {
    title: "Shorten a new link"
  });
  next()
});

router.post("/", (req, res, next) => {
  let link = req.body.link;
  let uniqueId = generateId(process.env.UNIQUE_STRINGS_LENGTH);

  let myLink = new Link({
    "originalLink": normalizeUrl(link),
    "uniqueId": uniqueId
  });

  myLink.save()
    .then(doc => {
      res.render("result", {
        shortLink: (`${process.env.HOSTNAME}/${doc.uniqueId}`)
      })
    })
    .catch(e => {
      next()
    })
});

module.exports = router;
