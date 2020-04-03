const cookieParser = require("cookie-parser");
const createError = require("http-errors");
const mongoose = require("mongoose");
const express = require("express");
const logger = require("morgan");
const path = require("path");

const url = "mongodb://localhost:27017/shortLinks";
mongoose.connect(url, { useNewUrlParser: true, useUnifiedTopology: true })
  .then(() => console.log("Database link established"))
  .catch(console.error);

const indexRouter = require("./routes/index");
const listRouter = require("./routes/list");

const app = express();

app.set("views", path.join(__dirname, "views"));
app.set("view engine", "hbs");

app.use(logger("dev"));
app.use(express.json());
app.use(express.urlencoded({ extended: false }));
app.use(cookieParser());

app.use(express.static(path.join(__dirname, "public")));

app.use("/", indexRouter);
app.use("/list", listRouter);

app.use((req, res, next) => {
  next(createError(404));
});

app.use((err, req, res, next) => {
  res.locals.message = err.message;
  res.locals.error = req.app.get("env") === "development" ? err : {};

  res.status(err.status || 500);
  res.render("error");
});

module.exports = app;
