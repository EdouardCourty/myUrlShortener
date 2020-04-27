const express = require('express');
let router = express.Router();

let Link = require("../models/Link");

router.get("/", async (req, res, next) => {
  let links = await Link.find();
  links.map(e => {
    e.__v = undefined;
    e.originalLink = e.originalLink.substring(0, 80);
    e.baseString = e.baseString.substring(0, 80);
  });

  res.render("list", {
    title: "List of the links",
    links: links,
    stylesheet: "list.css",
    script: "deleteListener.js"
  });
});

router.delete("/delete", (req, res, next) => {
  let password = req.query.password;
  let id = req.query.id;

  console.log(id, password);

  if (password !== "password" || !password) return res.redirect(303, "/list");

  if (id == "all") {
    Link.deleteMany()
      .then(n => {
        return res.json({
          "success": true,
          "deletedDocuments": n
        })
      })
      .catch(e => {
        return res.json({
          "success": false,
          "error": e
        })
      });
  } else if (id) {
    Link.findOneAndDelete({ "_id": id })
      .then(doc => {
        return res.json({
          "success": true,
          "doc": doc
        })
      })
      .catch(e => {
        return res.json({
          "success": false,
          "error": e
        })
      })
  }

  return res.redirect(303, "/list");
});

module.exports = router;
