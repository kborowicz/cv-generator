export default class Breakpoints {
    static sm(func) {
        Breakpoints.smMedia.addListener((mq) => {
            if (mq.matches) {
                func.onIncrease();
            } else {
                func.onDecrease();
            }
        });
        return this;
    }

    static md(func) {
        Breakpoints.mdMedia.addListener((mq) => {
            if (mq.matches) {
                func.onIncrease();
            } else {
                func.onDecrease();
            }
        });
        return this;
    }

    static lg(func) {
        Breakpoints.lgMedia.addListener((mq) => {
            if (mq.matches) {
                func.onIncrease();
            } else {
                func.onDecrease();
            }
        });
        return this;
    }

    static xl(func) {
        Breakpoints.xlMedia.addListener((mq) => {
            if (mq.matches) {
                func.onIncrease();
            } else {
                func.onDecrease();
            }
        });
        return this;
    }
}

Breakpoints.smMedia = window.matchMedia('(min-width: 576px)');
Breakpoints.mdMedia = window.matchMedia('(min-width: 768px)');
Breakpoints.lgMedia = window.matchMedia('(min-width: 992px)');
Breakpoints.xlMedia = window.matchMedia('(min-width: 1200px)');
Breakpoints.xxlMedia = window.matchMedia('(min-width: 1600px)');
