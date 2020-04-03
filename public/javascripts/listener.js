document.querySelector("a").addEventListener("click", e => {
  let textField = document.querySelector("input[type=text]");
  textField.className = "selected";
  textField.select();
  document.execCommand("copy");
  eraseSlowly(textField);
});

let finishedErasing = false;
let finishedWriting = false;
let textToWrite = "Link copied to clipboard.";

function eraseSlowly (elem) {
  if (finishedErasing) return writeTextSlowly(textToWrite, elem);
  setTimeout(() => {
    if (elem.value.length <= 0) setTimeout(() => {
      finishedErasing = true;
    }, 400);
    let newValue = elem.value.split("");
    newValue[newValue.length-1] = null;
    newValue = newValue.join("");
    elem.value = newValue;
    eraseSlowly(elem)
  }, 10);
}

function writeTextSlowly(text, elem) {
  if (finishedWriting) return;
  setTimeout(() => {
    if (elem.value.length === textToWrite.length - 1) finishedWriting = true;
    elem.value = elem.value + textToWrite[elem.value.length];
    writeTextSlowly(text, elem)
  }, 10);
}