const express = require('express');
let router = express.Router();

let { generateId, normalizeUrl } = require("../lib/utils");
let Link = require("../models/Link");

router.get('/', (req, res, next) => {
  res.json(process.env)
});

module.exports = router;
