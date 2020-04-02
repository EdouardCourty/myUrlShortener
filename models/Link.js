const mongoose = require("mongoose");

const link = new mongoose.Schema({
  "originalLink": String,
  "uniqueId": String,
  "createdAt": {
    type: Date,
    default: new Date()
  },
  "timesVisited": {
    type: Number,
    default: 0
  }
});

module.exports = mongoose.model("Link", link);