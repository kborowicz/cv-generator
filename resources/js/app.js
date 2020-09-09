import Modal from './components/modal.js';
import Toast from './components/toast.js';
import { id, el, on } from 'utils';

class App {
    constructor() {
        this.skillsContainer = id('js-skills');
        this.interestsContainer = id('js-interests');
        this.employmentHistoryContainer = id('js-employment-history');
        this.educationHistoryContainer = id('js-education-history');

        on('click', id('js-skills-add-btn'), () => this.addSkills());
        on('click', id('js-interests-add-btn'), () => this.addInterests());
        on('click', id('js-employment-add-btn'), () => this.addEmploymentHistory());
        on('click', id('js-education-add-btn'), () => this.addEducationHistory());
    }

    addSkills() {
        const row = el('div.row', [
            el('input.form-control', {
                type: 'text',
                placeholder: 'Skills'
            }),
            el('div.row__delete-icon', {
                onclick: () => this.skillsContainer.removeChild(row)
            }, el('i.ic-x'))
        ]);

        this.skillsContainer.appendChild(row);
    }

    addInterests() {
        const row = el('div.row', [
            el('input.form-control', {
                type: 'text',
                placeholder: 'Interests'
            }),
            el('div.row__delete-icon', {
                onclick: () => this.interestsContainer.removeChild(row)
            }, el('i.ic-x'))
        ]);

        this.interestsContainer.appendChild(row);
    }

    addEmploymentHistory() {
        const row = el('div.row.row--history', [
            el('div', {
                style: {
                    flex: 1
                }
            }, [
                el('div.row', [
                    el('input.form-control.col--1-3', {
                        placeholder: 'Perdiod'
                    }),
                    el('input.form-control.col--2-3', {
                        placeholder: 'Title'
                    })
                ]),
                el('textarea.form-control.form-control--textarea', {
                    placeholder: 'Description'
                })
            ]
            ),
            el('div.row__delete-icon', {
                onclick: () => this.employmentHistoryContainer.removeChild(row)
            }, el('i.ic-x'))
        ]);

        this.employmentHistoryContainer.appendChild(row);
    }

    addEducationHistory() {
        const row = el('div.row.row--history', [
            el('div', {
                style: {
                    flex: 1
                }
            }, [
                el('div.row', [
                    el('input.form-control.col--1-3', {
                        placeholder: 'Perdiod'
                    }),
                    el('input.form-control.col--2-3', {
                        placeholder: 'Title'
                    })
                ]),
                el('textarea.form-control.form-control--textarea', {
                    placeholder: 'Description'
                })
            ]
            ),
            el('div.row__delete-icon', {
                onclick: () => this.educationHistoryContainer.removeChild(row)
            }, el('i.ic-x'))
        ]);

        this.educationHistoryContainer.appendChild(row);
    }

    saveChanges() {

    }
}

export default new App();
