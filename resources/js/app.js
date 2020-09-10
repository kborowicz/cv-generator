import Toast from './components/toast.js';
import anime from 'anime';
import { id, el, on, ajax } from 'utils';

class App {
    constructor() {
        this.skills = [];
        this.interests = [];
        this.employmentHistory = [];
        this.educationHistory = [];

        this.baseDataInputs = {
            name: id('js-basedata-name'),
            lastname: id('js-basedata-lastname'),
            birthDate: id('js-basedata-birth-date'),
            phoneNumber: id('js-basedata-phone-number'),
            email: id('js-basedata-email'),
            streetAndHouseNumber: id('js-basedata-street-and-house-number'),
            townAndZipCode: id('js-basedata-town-and-zipcode'),
            rodo: id('js-basedata-rodo'),
            githubLink: id('js-basedata-github')
        };

        this.userImage = id('js-user-image');
        this.skillsContainer = id('js-skills');
        this.interestsContainer = id('js-interests');
        this.employmentHistoryContainer = id('js-employment-history');
        this.educationHistoryContainer = id('js-education-history');

        const userName = id('js-user-name');
        const userLastname = id('js-user-lastname');
        const imageUploadInput = id('js-image-upload-input');

        on(['input', 'paste'], this.baseDataInputs.name, function() {
            userName.innerText = this.value;
        });

        on(['input', 'paste'], this.baseDataInputs.lastname, function() {
            userLastname.innerText = this.value;
        });

        on('change', imageUploadInput, () => {
            if (imageUploadInput.files.length > 0) {
                this.uploadImage(imageUploadInput.files[0]);
            }
        });

        on('click', id('js-skills-add-btn'), () => this.addSkills());
        on('click', id('js-interests-add-btn'), () => this.addInterests());
        on('click', id('js-employment-add-btn'), () => this.addEmploymentHistory());
        on('click', id('js-education-add-btn'), () => this.addEducationHistory());
        on('click', id('js-save-btn'), () => !this.saving && this.saveData());

        ajax({
            method: 'get',
            url: '/cv-generator/get-data',
            returnType: 'json'
        }).then(response => {
            if (response.data.base) {
                Object.keys(response.data.base).forEach(key => {
                    if (this.baseDataInputs[key]) {
                        this.baseDataInputs[key].value = response.data.base[key];
                    }
                });
            }

            if (response.data.skills) {
                response.data.skills.forEach(el => {
                    const input = this.addSkills();
                    input.value = el;
                });
            }

            if (response.data.interests) {
                response.data.interests.forEach(el => {
                    const input = this.addInterests();
                    input.value = el;
                });
            }

            if (response.data.employmentHistory) {
                response.data.employmentHistory.forEach(el => {
                    const inputs = this.addEmploymentHistory();
                    inputs.period.value = el.period;
                    inputs.title.value = el.title;
                    inputs.description.value = el.description;
                });
            }

            if (response.data.educationHistory) {
                response.data.educationHistory.forEach(el => {
                    const inputs = this.addEducationHistory();
                    inputs.period.value = el.period;
                    inputs.title.value = el.title;
                    inputs.description.value = el.description;
                });
            }

            const loadingOverlay = id('js-loading-overlay');

            anime({
                targets: loadingOverlay,
                duration: 300,
                easing: 'linear',
                opacity: [1, 0],
                complete: () => {
                    loadingOverlay.style.display = 'none';
                }
            });
        });
    }

    addSkills() {
        let input;

        const row = el('div.row', [
            input = el('input.form-control', {
                type: 'text',
                placeholder: 'Skills'
            }),
            el('div.row__delete-icon', {
                onclick: () => {
                    this.skills.splice(this.skills.indexOf(input), 1);
                    this.skillsContainer.removeChild(row);
                }
            }, el('i.ic-x'))
        ]);

        this.skills.push(input);
        this.skillsContainer.appendChild(row);

        return input;
    }

    addInterests() {
        let input;

        const row = el('div.row', [
            input = el('input.form-control', {
                type: 'text',
                placeholder: 'Interests'
            }),
            el('div.row__delete-icon', {
                onclick: () => {
                    this.interests.splice(this.interests.indexOf(input), 1);
                    this.interestsContainer.removeChild(row);
                }
            }, el('i.ic-x'))
        ]);

        this.interests.push(input);
        this.interestsContainer.appendChild(row);

        return input;
    }

    addEmploymentHistory() {
        const inputs = {};

        const row = el('div.row.row--history', [
            el('div', {
                style: {
                    flex: 1
                }
            }, [
                el('div.row', [
                    inputs.period = el('input.form-control.col--1-3', {
                        placeholder: 'Perdiod'
                    }),
                    inputs.title = el('input.form-control.col--2-3', {
                        placeholder: 'Title'
                    })
                ]),
                inputs.description = el('textarea.form-control.form-control--textarea', {
                    placeholder: 'Description'
                })
            ]
            ),
            el('div.row__delete-icon', {
                onclick: () => {
                    this.employmentHistory.splice(this.employmentHistory.indexOf(inputs), 1);
                    this.employmentHistoryContainer.removeChild(row);
                }
            }, el('i.ic-x'))
        ]);

        this.employmentHistory.push(inputs);
        this.employmentHistoryContainer.appendChild(row);

        return inputs;
    }

    addEducationHistory() {
        const inputs = {};

        const row = el('div.row.row--history', [
            el('div', {
                style: {
                    flex: 1
                }
            }, [
                el('div.row', [
                    inputs.period = el('input.form-control.col--1-3', {
                        placeholder: 'Perdiod'
                    }),
                    inputs.title = el('input.form-control.col--2-3', {
                        placeholder: 'Title'
                    })
                ]),
                inputs.description = el('textarea.form-control.form-control--textarea', {
                    placeholder: 'Description'
                })
            ]
            ),
            el('div.row__delete-icon', {
                onclick: () => {
                    this.educationHistory.splice(this.educationHistory.indexOf(inputs), 1);
                    this.educationHistoryContainer.removeChild(row);
                }
            }, el('i.ic-x'))
        ]);

        this.educationHistory.push(inputs);
        this.educationHistoryContainer.appendChild(row);

        return inputs;
    }

    saveData() {
        this.saving = true;

        const base = {};
        Object.keys(this.baseDataInputs).forEach(key => {
            base[key] = this.baseDataInputs[key].value;
        });

        const skills = this.skills.filter(el => el.value.length > 0).map(el => el.value);
        const interests = this.interests.filter(el => el.value.length > 0).map(el => el.value);

        const employmentHistory = this.employmentHistory
            .filter(el => el.title.value.length > 0)
            .map(el => ({
                period: el.period.value,
                title: el.title.value,
                description: el.description.value
            }));

        const educationHistory = this.educationHistory
            .filter(el => el.title.value.length > 0)
            .map(el => ({
                period: el.period.value,
                title: el.title.value,
                description: el.description.value
            }));

        ajax({
            method: 'post',
            url: '/cv-generator/save-data',
            returnType: 'json',
            data: {
                base: base,
                skills: skills,
                interests: interests,
                employmentHistory: employmentHistory,
                educationHistory: educationHistory,
                token: window.csrf
            }
        }).then(response => {
            this.saving = false;

            if (response.success) {
                Toast.show('success', response.success);
            } else if (response.error) {
                Toast.show('error', response.error);
            }
        });
    }

    uploadImage(file) {
        const formData = new FormData();
        formData.append('imageFile', file);

        ajax({
            method: 'post',
            url: '/cv-generator/upload-image',
            returnType: 'json',
            data: formData,
            headers: null
        }).then(response => {
            if (response.data) {
                this.userImage.src = response.data;
            } else if (response.error) {
                Toast.show('error', response.error);
            }
        });
    }
}

export default new App();
