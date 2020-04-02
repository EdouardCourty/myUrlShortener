const express = require('express');
let router = express.Router();

let Link = require("../models/Link");

router.get("/", (req, res, next) => {
  res.render("list", {
    title: "List of the links"
  });
  next()
});

router.delete("/", (req, res, next) => {

});

module.exports = router;
