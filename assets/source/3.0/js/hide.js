const removeEmpty = (item) => {
  if(item.innerHTML.trim() == '') {
    item.innerHTML = '';
  }
}

const findEmpty = (event) => {
  let targetDivs = document.querySelectorAll('.u-hide-empty'); 

  if(targetDivs.length > 0) {
    [].forEach.call(targetDivs, removeEmpty);
  }
}

export default (() => {
  window.addEventListener('DOMContentLoaded', findEmpty);
})();