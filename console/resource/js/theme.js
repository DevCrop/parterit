const theme = () => {
    const THEME_LIGHT = 'light';
    const THEME_DARK = 'dark';

    const htmlEl = document.documentElement;
    const themeBtn = document.getElementById('toggle');
    const sunIcon = document.querySelector('.toggle .bxs-sun');
    const moonIcon = document.querySelector('.toggle .bx-moon');

    const theme = {
        set localStorage(val) {
            localStorage.setItem('theme', val);
            htmlEl.dataset.theme = val;
        },
        get localStorage() {
            return localStorage.getItem('theme');
        },
    };

    if (theme.localStorage === null) {
        theme.localStorage = THEME_LIGHT;
    } else {
        theme.localStorage = theme.localStorage;
    }

    const changeTheme = () => {
        const themeMode =
            theme.localStorage === THEME_LIGHT ? THEME_DARK : THEME_LIGHT;
        theme.localStorage = themeMode;

        if (sunIcon) {
            sunIcon.className =
                sunIcon.className == 'bx bxs-sun' ? 'bx bx-sun' : 'bx bxs-sun';
        }
        if (moonIcon) {
            moonIcon.className =
                moonIcon.className == 'bx bxs-moon' ? 'bx bx-moon' : 'bx bxs-moon';
        }
    };

    if (themeBtn) {
        themeBtn.addEventListener('click', changeTheme);
    }
};

theme();
