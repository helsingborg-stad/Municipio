export default function fixedMenu(Modularity) {

    let header = document.getElementById('fixed-header');
    let stuck = false;
    let stickPoint = getDistance();
    
    function getDistance() {
      let topDist = header.offsetTop;

      return topDist;
    }
    
    window.onscroll = function(e) {
        let distance = header.getBoundingClientRect().top;
        let offset = window.pageYOffset;

        if ((distance <= 0) && !stuck) {
            header.classList.add('is-sticky');
            stuck = true;
        } else if (stuck && (offset <= stickPoint)) {
            header.classList.remove('is-sticky');
            stuck = false;
        }
    }
}