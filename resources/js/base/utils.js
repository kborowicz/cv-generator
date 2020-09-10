/* kdom */

/**
 *
 * @param {*} item
 */
export function isObject(item) {
    return item && typeof item === 'object' && !Array.isArray(item);
}

/**
 *
 * @param {*} item
 */
export function isElement(item) {
    return item && (item instanceof Element || item instanceof HTMLDocument);
}

/**
 *
 * @param {*} el
 * @param {*} attributes
 */
export function attr(el, attributes) {
    for (const [key, value] of Object.entries(attributes)) {
        if (!value) {
            continue;
        }

        if (key === 'style') {
            if (isObject(value)) {
                for (const style in value) {
                    el.style[style] = value[style];
                }
            } else {
                throw new Error('style property must be an object');
            }
        } else if (key === 'dataset') {
            if (isObject(value)) {
                for (const data in value) {
                    el.dataset[data] = value[data];
                }
            } else {
                throw new Error('dataset property must be an object');
            }
        } else if (key === 'class') {
            if (Array.isArray(value)) {
                value.forEach(className => {
                    el.classList.add(className);
                });
            } else {
                throw new Error('class property must be an array');
            }
        } else if (key in el || typeof value === 'function') {
            el[key] = value;
        } else {
            el.setAttribute(key, value);
        }
    }
}

/**
 *
 * @param {*} query
 * @param {*} arg1
 * @param {*} arg2
 */
export function el(query, arg1, arg2) {
    if (typeof query !== 'string') {
        throw new TypeError('query must be a string');
    }

    if (query === 'text') {
        if (typeof arg1 === 'string') {
            return document.createTextNode(arg1);
        } else {
            throw new TypeError('arg1 must be a string for \'text\' type');
        }
    }

    const querySplit = (query || 'div').replace(/\s+/g, '.').split('.');
    const element = document.createElement(querySplit[0]);

    for (let i = 1; i < querySplit.length; i++) {
        element.classList.add(querySplit[i]);
    }

    if (arg1) {
        if (isObject(arg1)) {
            if (isElement(arg1)) {
                element.append(arg1);
            } else {
                attr(element, arg1);
            }
        } else if (Array.isArray(arg1)) {
            arg1.forEach(el => {
                if (el) {
                    element.append(el);
                }
            });
        } else {
            element.append(arg1);
        }
    }

    if (arg2) {
        if (Array.isArray(arg2)) {
            arg2.forEach(el => {
                if (el) {
                    element.append(el);
                }
            });
        } else {
            element.append(arg2);
        }
    }

    return element;
}

export function remove(el) {
    if (el.parentNode) {
        el.parentNode.removeChild(el);
    }
}

export function text(text) {
    return document.createTextNode(text);
}

/**
 * Shorcut for getElementById method
 * @param {*} elementId id of the element
 */
export function id(elementId) {
    return document.getElementById(elementId);
}

/**
 * Shorcut for querySelectorAll method
 * @param {*} query
 */
export function query(query) {
    return [...document.querySelectorAll(query)];
}

export function bounds(el) {
    return el.getBoundingClientRect();
}

export function style(el, styles) {
    for (const style in styles) {
        el.style[style] = styles[style];
    }

    return el;
}

export function on(events, targets, func) {
    if (!events || !targets) {
        throw new Error('\'events\' and \'targets\' must not be null');
    }

    let eventsArray;
    let targetsArray;

    if (Array.isArray(events)) {
        eventsArray = events;
    } else if (typeof events === 'string') {
        eventsArray = [events];
    } else {
        throw new TypeError('\'events\' must be either an Array or string.');
    }

    if (Array.isArray(targets)) {
        targetsArray = targets;
    } else if (isObject(targets)) {
        targetsArray = [targets];
    } else if (typeof targets === 'string') {
        targetsArray = [...document.querySelectorAll(targets)];
    } else {
        throw new TypeError('\'targets\' must be either an Array, Element or query string.');
    }

    targetsArray.forEach(el => {
        eventsArray.forEach(ev => {
            el.addEventListener(ev, func, true);
        });
    });

    return func;
}

/**
 * Removes event listener of specified type from target
 * @param {*} event event type
 * @param {*} target target element
 * @param {*} func function registered for event
 */
export function off(event, target, func) {
    target.removeEventListener(event, func);
}

/**
 * Removes all children from element
 * @param {*} el element
 */
export function empty(el) {
    while (el.firstChild) {
        el.removeChild(el.lastChild);
    }

    return el;
}

/**
 * Picks specified properties from object o
 * @param {*} o source object
 * @param  {...any} properties properties to pick
 */
export function pick(o, ...properties) {
    return Object.assign({}, ...properties.map(property => {
        return { [property]: o[property] };
    }));
}

export function pickStyle(el, ...styles) {
    const computedStyle = getComputedStyle(el);

    return Object.assign({}, ...styles.map(property => {
        return { [property]: el.style[property] ? el.style[property] : computedStyle[property] };
    }));
}

/**
 * Clamp value in range specified by min and max
 * @param {*} value value to be clamped
 * @param {*} min min value
 * @param {*} max max value
 */
export function clamp(value, min, max) {
    return value < min ? min : value > max ? max : value;
}

/**
 * Convert any css value expression (eg. .25rem, calc(20em + 2.5in)) to pixels
 * @param {*} expression
 */
export function px(expression, parent = document.body) {
    const tempElement = el('div', {
        style: {
            marginTop: expression
        }
    });

    parent.append(tempElement);

    const computedWidth = getComputedStyle(tempElement).marginTop;

    parent.removeChild(tempElement);

    return parseFloat(computedWidth);
}

export function serialize(object) {
    if (!object) {
        return '';
    }

    const tokens = [];

    const extractToken = (key, value, prefix) => {
        if (Array.isArray(value)) {
            const subPrefix = prefix + ((prefix.length == 0) ? key : '[' + key + ']');
            value.forEach((el, i) => {
                extractToken(i, el, subPrefix);
            });
        } else if (value instanceof Object) {
            const subPrefix = prefix + ((prefix.length == 0) ? key : '[' + key + ']');
            Object.keys(value).forEach(key => {
                extractToken(key, value[key], subPrefix);
            });
        } else {
            const ecnodedValue = encodeURIComponent(value);
            tokens.push((prefix.length == 0) ? key + '=' + ecnodedValue : prefix + '[' + key + ']=' + ecnodedValue);
        }
    };

    Object.keys(object).forEach(key => {
        extractToken(key, object[key], '');
    });

    return tokens.join('&');
}

export function ajax(settings) {
    const defaults = {
        method: 'GET',
        returnType: 'text', // text | xml | json
        url: null,
        data: null,
        headers: {
            'content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
        }
    };

    const s = Object.assign({}, defaults, (typeof settings === 'object') ? settings : { url: settings });

    if (!s.url) {
        throw new Error('URL is not defined');
    }

    return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();

        xhr.onreadystatechange = function() {
            if (this.readyState == 4) {
                if (this.status == 200) {
                    switch (s.returnType) {
                    case 'text':
                        resolve(xhr.responseText);
                        break;
                    case 'json':
                        try {
                            resolve(JSON.parse(xhr.responseText));
                        } catch (e) {
                            reject(e);
                        }
                        break;
                    case 'xml':
                        resolve(xhr.responseXML);
                        break;
                    default:
                        reject(new Error('Unknown return type: ' + s.returnType));
                        break;
                    }
                } else {
                    reject(new Error('Request failed with status: ' + xhr.status));
                }
            }
        };

        if (s.method.toUpperCase() == 'GET') {
            xhr.open('GET', s.url + '?' + serialize(s.data), true);

            if (isObject(s.headers)) {
                Object.keys(s.headers).forEach(header => {
                    xhr.setRequestHeader(header, s.headers[header]);
                });
            }

            xhr.send();
        } else if (s.method.toUpperCase() == 'POST') {
            xhr.open('POST', s.url, true);

            if (isObject(s.headers)) {
                Object.keys(s.headers).forEach(header => {
                    xhr.setRequestHeader(header, s.headers[header]);
                });
            }

            if (s.data instanceof FormData) {
                xhr.send(s.data);
            } else {
                xhr.send(serialize(s.data));
            }
        }
    });
}
