import { el } from 'utils';
import anime from 'anime';

export default class Toast {
    constructor(type = 'info', content = 'toast content', options) {
        this.type = type;
        this.content = content;

        const defaults = {
            showDuration: 400,
            hideDuration: 400,
            timeout: 6000,
            extendedTimeout: 2000,
            typeIcon: true,
            closeButton: true,
            autohide: true,
            userselectNone: true,
            onshow: () => {},
            onhide: () => {}
        };

        Object.assign(this, defaults, options);
    }

    createDOM() {
        let typeIcon;
        let closeButton;

        if (this.typeIcon) {
            typeIcon = el('div.toast-type', el(`i.${Toast.settings.icons[this.type]}`));
        }

        if (this.closeButton) {
            closeButton = el('div.toast-close', el(`i.toast-close-icon.${Toast.settings.icons.close}`, {
                onclick: () => this.hide()
            }));
        }

        this.dom = el('div.toast', {
            dataset: {
                toastType: this.type
            },
            role: 'dialog',
            ontouchstart: () => this.hide()
        }, [
            el('div.toast-content', [
                typeIcon,
                el('div.toast-text', {
                    style: {
                        userSelect: this.userselectNone ? 'none' : 'initial'
                    }
                }, this.content),
                closeButton
            ]),
            (this.progressBar = el('div.toast-progress'))
        ]);
    }

    show() {
        this.createDOM();

        if (Toast.container.parentNode !== document.body) {
            document.body.append(Toast.container);
        }

        Toast.container.append(this.dom);

        anime({ // Show animation
            targets: this.dom,
            duration: this.showDuration,
            easing: 'easeOutCubic',
            height: [0, this.dom.offsetHeight],
            opacity: [0, 1],
            complete: () => {
                this.dom.style.height = null;
                this.onshow();
            }
        });

        this.progressBarAnimation = anime({
            targets: this.progressBar,
            duration: this.showDuration + this.timeout,
            autoplay: this.autohide,
            easing: 'linear',
            width: ['100%', 0],
            complete: () => this.hide()
        });

        if (Toast.stack.length + 1 > Toast.settings.maxStack) {
            for (let i = 0; i < Toast.stack.length - Toast.settings.maxStack + 1; i++) {
                Toast.stack[i].hide();
            }
        }

        Toast.stack.push(this);
    }

    hide() {
        anime({
            targets: this.dom,
            duration: this.hideDuration,
            opacity: 0,
            left: '-10%',
            easing: 'easeOutCubic',
            complete: () => {
                Toast.remove(this);
                this.onhide();

                if (this.progressBarAnimation) {
                    this.progressBarAnimation.pause();
                    delete this.progressBarAnimation;
                }
            }
        });
    }

    /*

    Static methods

    */

    static show(type, content, options) {
        const toast = (type instanceof Toast)
            ? type
            : new Toast(type, content, options);

        toast.show();
    }

    static remove(toast) {
        const index = Toast.stack.indexOf(toast);

        if (index > -1) {
            Toast.stack.splice(index, 1);
        }

        if (toast.dom) {
            Toast.container.removeChild(toast.dom);
            delete toast.dom;
        }

        if (Toast.stack.length == 0 && Toast.container.parentNode === document.body) {
            document.body.removeChild(Toast.container);
        }
    }

    static hideAll() {
        [...Toast.stack].forEach(t => t.hide());
    }
}

/*

Static fields

*/

Toast.container = el('div.toast-stack', {

    // Frezee toasts
    onmouseenter: () => {
        Toast.stack.forEach(t => {
            if (t.autohide) {
                anime.set(t.progressBar, {
                    width: '100%'
                });

                if (t.progressBarAnimation) {
                    t.progressBarAnimation.pause();
                    delete t.progressBarAnimation;
                }
            }
        });
    },

    // Unfreze toasts and hide after extendedTimeout
    onmouseleave: () => {
        Toast.stack.forEach(t => {
            if (t.autohide) {
                t.progressBarAnimation = anime({
                    targets: t.progressBar,
                    duration: t.extendedTimeout,
                    easing: 'linear',
                    width: ['100%', 0]
                });

                t.progressBarAnimation.finished.then(() => t.hide());
            }
        });
    }
});

Toast.stack = [];

Toast.settings = {
    maxStack: 3,
    icons: {
        info: 'ic-message-circle',
        success: 'ic-check',
        warning: 'ic-alert-triangle',
        error: 'ic-alert-circle',
        option: 'ic-help-circle',
        close: 'ic-x'
    }
};
