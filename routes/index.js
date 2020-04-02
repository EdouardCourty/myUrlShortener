const express = require('express');
let router = express.Router();

let { generateId, normalizeUrl } = require("../lib/utils");
let Link = require("../models/Link");

/* GET home page. */
router.get('/:uniqueId', (req, res, next) => {
  Link.findOne({ "uniqueId": req.params.uniqueId })
    .then(doc => {
      let url = doc.originalLink;
      return doc && url
        ? res.redirect(url)
        : next()
    })
    .catch(e => next())
});

router.get("/", (req, res, next) => {
  res.render("index", {
    title: "Shorten a new link"
  })
});

router.post("/", (req, res, next) => {
  let link = req.query.link;
  let uniqueId = generateId(process.env.UNIQUE_STRINGS_LENGHT);

  let myLink = new Link({
    "originalLink": normalizeUrl(link),
    "uniqueId": uniqueId
  });

  myLink.save()
    .then(doc => {
      res.json({
        "success": true,
        "document": doc
      })
    })
    .catch(e => {
      next()
    })
});

module.exports = router;
