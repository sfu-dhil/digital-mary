/* Basic scripts for the Digital Mary Project
 */

/* First thing to do is set up the images for the viewer thing */

const toolbarOpts = ['zoomIn', 'zoomOut', 'oneToOne', 'reset', 'prev', 'next','rotateLeft','rotateRight'];
let gallery;
let ndmViewerContainer = document.querySelector('#ndm-viewer-container');

resizeCarousel();
makeImageViewer();

function resizeCarousel() {
    let carousel = document.getElementById('carousel');
    if (carousel) {
        let imgs = carousel.querySelectorAll('img');
        let srcs = Array.from(imgs).map(img => img.src);
        let heights = [];
        srcs.forEach(src => {
            heights.push(new Promise((resolve, reject) => {
                let tmpImg = new Image;
                tmpImg.onload = function () {
                    resolve(tmpImg.height);
                }
                tmpImg.src = src;
            }))
        })
        Promise.all(heights).then(hs => {
            let maxHeight = Math.max(...hs);
            carousel.style.height = (maxHeight + 100) + "px";
        })
    }
}



function makeImageViewer(){
    let imgCtr = document.querySelector('.images');
    let images = imgCtr.querySelectorAll('a[data-img]');
    let toolbar = {};
    let show = 1;
    toolbarOpts.forEach(opt => {
        if ((opt === 'next' || opt === 'prev') && images.length < 2){
            show = 0;
        }
        toolbar[opt] = {};
        toolbar[opt]['show'] = show;
        toolbar[opt]['size'] = 'large';
    });

    /*  We need to figure out some way to get the gallery transitions
        to work a bit better: we don't want the image loading in, but we
        do want it in all other cases.
     */


    gallery = new Viewer(imgCtr, {
        url: function(img){
            let src = img.parentNode.getAttribute('data-img');
            return src;
        },
        transition: true,
        container: ndmViewerContainer,
        toolbar
    });

    images.forEach( a => {
        a.classList.add('zoomable');
        a.addEventListener('click', e => {
            e.preventDefault();
            imgCtr.focus();
        })
    })

}


