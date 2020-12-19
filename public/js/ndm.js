/* Basic scripts for the Digital Mary Project
 */

/* First thing to do is set up the images for the viewer thing */

const toolbarOpts = ['zoomIn', 'zoomOut', 'oneToOne', 'reset', 'prev', 'next','rotateLeft','rotateRight'];
let gallery,  slider;
let imgSlider = document.querySelector('.image-slider');
let ndmViewerContainer = document.querySelector('#ndm-viewer-container');

// First add the JS class
document.querySelector('body').classList.add('js');

// Now enhance the lazy loading
enhanceLazyLoad();

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

// Toggle for administrative stuff
document.querySelector('.admin-toggle').addEventListener('change', e=>{
    document.body.classList.toggle('hideAdmin');
})


// Hacks for fixing up the descriptions

// Clean up some descriptions, but this is a hack
let nbspRex = /^\s*&nbsp;\s*$/
document.querySelectorAll('.item-description').forEach(div => {
    div.querySelectorAll('p').forEach(p => {
        let text = p.innerHTML;
        if (nbspRex.test(text)){
            p.parentElement.removeChild(p);
        }
    })
});


/* Add special viewer stuff */
if (ndmViewerContainer){
    makeImageViewer();
    makeGlider();
    makeImageTools();
}


function makeGlider(){
    let slides = imgSlider.querySelectorAll('.item');
    if (slides.length > 1){
        // Set up a new glider
        let glider = new Glider(imgSlider, {
            scrollLock: true,
            dots: '.dots',
            arrows: {
                prev: '.slider-btn-prev',
                next: '.slider-btn-next'
            },
            duration: 1
        });

    }
}

function makeImageTools(){
    imgSlider.querySelectorAll('.img-toolbar__tools a:not([href])').forEach(link => {
            let div = link.closest('div[id]');
            let i = Array.from(div.parentElement.children).indexOf(div);
            link.addEventListener('click', e => {
                e.preventDefault();
                if (link.classList.contains('img-tool-info')){
                    imgSlider.querySelectorAll('details > summary').forEach(s => s.click());
                } else if (link.classList.contains('img-tool-zoom')){
                    gallery.view(i);
                }
            })
        }
    )
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


// Let's do some browse lazy loading...

function enhanceLazyLoad(){
    if ('loading' in HTMLImageElement.prototype){
        let lazyImages = document.querySelectorAll('img[loading="lazy"]');
        lazyImages.forEach(img => {
            let item = img.closest('.item');
            if (!img.complete) {
                item.classList.add('loading');
                img.addEventListener('load',  e => {
                    item.classList.add('loaded');
                });
            }
        });
    }

}




