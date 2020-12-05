/* Basic scripts for the Digital Mary Project
 */

/* First thing to do is set up the images for the viewer thing */

const toolbarOpts = ['zoomIn', 'zoomOut', 'oneToOne', 'reset', 'prev', 'next','rotateLeft','rotateRight'];
let gallery,  slider;
let imgSlider = document.querySelector('.image-slider');
let ndmViewerContainer = document.querySelector('#ndm-viewer-container');


// First add the JS class
document.querySelector('body').classList.add('js');

/* Make hamburger work */
document.querySelectorAll('.hamburger').forEach(ham => {
    ham.addEventListener('click', e => {
        ham.classList.toggle('is-active');
    })
})



// Now make details
document.querySelectorAll('details').forEach(el => {
    let accordion = new Accordion(el);
})




/* Add special viewer stuff */
if (ndmViewerContainer){
    makeImageViewer();

    if (imgSlider.querySelectorAll('.item').length > 1){
        slider = new Glider(imgSlider, {
            scrollLock: true,
            arrows: {
                prev: '.slider-btn-prev',
                next: '.slider-btn-next'
            },
            duration: 1
        });
        /* Now make our custom dots */
        let previewLinks = document.querySelectorAll('.image-preview a');
        let active = 0;
        const resetActive = function(n){
            if (active !== n){
                previewLinks[active].classList.remove('active');
                previewLinks[n].classList.add('active');
                active = n;
            }
        }

        previewLinks.forEach((link, i) => {
            link.addEventListener('click', e => {
                e.preventDefault();
                slider.scrollItem(i);
                resetActive(i);
            })
        })
        imgSlider.addEventListener('glider-slide-visible', e=> {
                let slide = e.detail.slide;
                resetActive(slide);
        })
    }
}



function makeImageViewer(){
    let imgCtr = document.querySelector('.image-slider');
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


