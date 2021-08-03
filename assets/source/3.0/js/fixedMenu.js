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
            header.style.position = 'fixed';
            header.style.width = '100%';
            header.style.top = '0px';
            stuck = true;
        } else if (stuck && (offset <= stickPoint)) {
            header.style.position = 'static';
            stuck = false;
        }
    }
}