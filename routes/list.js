const express = require('express');
let router = express.Router();

let Link = require("../models/Link");

router.get("/", async (req, res, next) => {
  let links = await Link.find();
  links.map(e => {
    e.__v = undefined
  });

  res.render("list", {
    title: "List of the links",
    links: links,
    stylesheet: "list.css"
  });
});

router.post("/delete", (req, res, next) => {
  let password = req.body.password;
  if (password !== "password" || !password) return res.redirect("/list");
  let toDelete = req.body.toDelete;

  if (toDelete == "all") {
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
  } else if (toDelete) {
    Link.findOneAndDelete({ "_id": toDelete })
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

  return res.redirect("/list");
});

module.exports = router;
