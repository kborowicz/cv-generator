import { el } from 'utils';
import anime from 'anime';

export default class Modal {
    constructor(options) {
        const defaults = {
            size: 'md',
            type: undefined,
            style: undefined,
            content: 'modal content',
            zIndex: 999,
            showDuration: 300,
            hideDuration: 300,
            closeButton: true,
            autoshow: false,
            onshow: () => {},
            onhide: () => {}
        };

        Object.assign(this, defaults, options);

        if (this.autoshow) {
            this.show();
        }
    }

    createDOM() {
        this.dom = el('div.modal', {
            style: this.style,
            dataset: {
                modalSize: this.size
            },
            role: 'dialog'
        }, this.content);

        this.container = el('div.modal-container', {
            style: {
                zIndex: this.zIndex
            }
        }, this.dom);

        document.body.append(this.container);
    }

    show() {
        this.createDOM();

        anime({
            targets: this.container,
            duration: this.showDuration,
            easing: 'cubicBezier(.09,.39,.65,1)',
            opacity: [0, 1]
        });

        anime({
            targets: this.dom,
            duration: this.showDuration,
            easing: 'cubicBezier(.09,.39,.65,1)',
            translateY: ['-100px', 0],
            opacity: [0, 1]
        });
    }

    hide() {
        anime({
            targets: this.container,
            duration: this.hideDuration,
            easing: 'cubicBezier(.35,0,.65,1)',
            opacity: [1, 0]
        });

        anime({
            targets: this.dom,
            duration: this.hideDuration,
            easing: 'cubicBezier(.35,0,.65,1)',
            translateY: [0, '50px'],
            opacity: [1, 0],
            complete: () => {
                document.body.removeChild(this.container);
                delete this.container;
                delete this.dom;
            }
        });
    }
}
