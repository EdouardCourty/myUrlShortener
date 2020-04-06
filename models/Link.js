const mongoose = require("mongoose");

const link = new mongoose.Schema({
  "originalLink": String,
  "uniqueId": String,
  "createdAt": {
    type: String,
    default: getDate()
  },
  "timesVisited": {
    type: Number,
    default: 0
  },
  "baseString": String
});

module.exports = mongoose.model("Link", link);

function getDate() {
  let dt = new Date();
  return `${
    (dt.getMonth()+1).toString().padStart(2, '0')}/${
    dt.getDate().toString().padStart(2, '0')}/${
    dt.getFullYear().toString().padStart(4, '0')} ${
    dt.getHours().toString().padStart(2, '0')}:${
    dt.getMinutes().toString().padStart(2, '0')}:${
    dt.getSeconds().toString().padStart(2, '0')}`
}