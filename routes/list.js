const express = require('express');
let router = express.Router();

let Link = require("../models/Link");

router.get("/", async (req, res, next) => {
  let links = await Link.find();
  links.map(e => JSON.stringify(e, null, 2));

  res.render("list", {
    title: "List of the links",
    links: links,
    stylesheet: "list.css"
  });
  next()
});

router.delete("/", (req, res, next) => {

});

module.exports = router;
