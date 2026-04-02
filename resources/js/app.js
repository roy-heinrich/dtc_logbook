import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Register stickyHeader BEFORE Alpine.start()
Alpine.data('stickyHeader', () => ({
	scrolled: false,
	hidden: false,
	mobileMenuOpen: false,
	threshold: 50,
	lastScrollY: 0,
	
	handleScroll() {
		const currentScrollY = window.scrollY;
		
		// Check if scrolled past threshold
		this.scrolled = currentScrollY > this.threshold;
		
		// Hide header when scrolling down, show when scrolling up
		if (currentScrollY > this.lastScrollY + 10) {
			this.hidden = true;
		} else if (currentScrollY < this.lastScrollY - 10) {
			this.hidden = false;
		}
		
		this.lastScrollY = currentScrollY;
	},

	toggleMobileMenu() {
		this.mobileMenuOpen = !this.mobileMenuOpen;
		document.body.classList.toggle('overflow-hidden', this.mobileMenuOpen);
	},

	closeMobileMenu() {
		this.mobileMenuOpen = false;
		document.body.classList.remove('overflow-hidden');
	}
}));

// Register datePicker BEFORE Alpine.start() so it's available when parsing x-data
Alpine.data('datePicker', (inputId, initialValue) => {
	const today = new Date();
	const parseDate = (value) => {
		if (!value) return null;
		const parts = value.split('-');
		if (parts.length !== 3) return null;
		const year = Number(parts[0]);
		const month = Number(parts[1]) - 1;
		const day = Number(parts[2]);
		const parsed = new Date(year, month, day);
		return Number.isNaN(parsed.getTime()) ? null : parsed;
	};
	const formatDate = (date) => {
		const year = date.getFullYear();
		const month = String(date.getMonth() + 1).padStart(2, '0');
		const day = String(date.getDate()).padStart(2, '0');
		return `${year}-${month}-${day}`;
	};

	return {
		open: false,
		digits: '',
		value: initialValue || '',
		displayMasked: '',
		month: 0,
		year: 0,
		days: [],
		init() {
			if (this.value) {
				const parts = this.value.split('-');
				this.digits = (parts[0] || '') + (parts[1] || '') + (parts[2] || '');
			}
			const initial = parseDate(this.value) || today;
			this.month = initial.getMonth();
			this.year = initial.getFullYear();
			this.refresh();
			this.buildCalendar();
		},
		get monthLabel() {
			const date = new Date(this.year, this.month, 1);
			return date.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
		},
		refresh() {
			const d = this.digits;
			const y = d.slice(0, 4).padEnd(4, 'Y');
			const m = d.slice(4, 6).padEnd(2, 'M');
			const dy = d.slice(6, 8).padEnd(2, 'D');
			this.displayMasked = `${y}-${m}-${dy}`;
			if (d.length === 8) {
				this.value = `${d.slice(0, 4)}-${d.slice(4, 6)}-${d.slice(6, 8)}`;
			} else {
				this.value = '';
			}
		},
		onMaskedInput(event) {
			let input = event.target.value.replace(/[^0-9]/g, '');
			if (input.length > 8) input = input.slice(0, 8);
			this.digits = input;
			this.refresh();
			if (input.length === 8) {
				const y = input.slice(0, 4);
				const m = input.slice(4, 6);
				const d = input.slice(6, 8);
				const parsed = parseDate(`${y}-${m}-${d}`);
				if (parsed) {
					this.month = parsed.getMonth();
					this.year = parsed.getFullYear();
					this.buildCalendar();
				}
			}
		},
		onBlur() {
			if (this.digits.length !== 8) {
				this.digits = '';
				this.value = '';
				this.refresh();
				return;
			}
			const y = this.digits.slice(0, 4);
			const m = this.digits.slice(4, 6);
			const d = this.digits.slice(6, 8);
			const parsed = parseDate(`${y}-${m}-${d}`);
			if (!parsed) {
				this.digits = '';
				this.value = '';
				this.refresh();
			} else {
				this.month = parsed.getMonth();
				this.year = parsed.getFullYear();
				this.buildCalendar();
			}
		},
		buildCalendar() {
			const first = new Date(this.year, this.month, 1);
			const last = new Date(this.year, this.month + 1, 0);
			const startDay = first.getDay();
			const days = [];
			for (let i = 0; i < startDay; i += 1) {
				days.push({ key: `empty-${i}`, label: '', date: null, isEmpty: true });
			}
			for (let day = 1; day <= last.getDate(); day += 1) {
				const date = new Date(this.year, this.month, day);
				days.push({ key: formatDate(date), label: day, date, isEmpty: false });
			}
			this.days = days;
		},
		isSelected(date) {
			return date && this.value === formatDate(date);
		},
		isToday(date) {
			return date && formatDate(date) === formatDate(today);
		},
		selectDate(date) {
			if (!date) return;
			this.value = formatDate(date);
			const parts = this.value.split('-');
			this.digits = parts[0] + parts[1] + parts[2];
			this.refresh();
			this.open = false;
		},
		selectToday() {
			this.selectDate(today);
		},
		clearDate() {
			this.digits = '';
			this.value = '';
			this.displayMasked = '';
			this.open = false;
		},
		prevMonth() {
			if (this.month === 0) {
				this.month = 11;
				this.year -= 1;
			} else {
				this.month -= 1;
			}
			this.buildCalendar();
		},
		nextMonth() {
			if (this.month === 11) {
				this.month = 0;
				this.year += 1;
			} else {
				this.month += 1;
			}
			this.buildCalendar();
		},
		prevYear() {
			this.year -= 1;
			this.buildCalendar();
		},
		nextYear() {
			this.year += 1;
			this.buildCalendar();
		},
	};
});

Alpine.start();

const themeKey = 'theme';

const setThemeClass = (mode) => {
	const root = document.documentElement;
	if (mode === 'dark') {
		root.classList.add('dark');
	} else {
		root.classList.remove('dark');
	}
};

const syncThemeToggle = () => {
	const toggle = document.querySelector('[data-theme-toggle]');
	if (!toggle) {
		return;
	}

	const isDark = document.documentElement.classList.contains('dark');
	if (toggle.tagName === 'INPUT' && toggle.type === 'checkbox') {
		toggle.checked = isDark;
		return;
	}

	toggle.setAttribute('aria-pressed', String(isDark));
	const sunIcon = toggle.querySelector('[data-theme-icon-sun]');
	const moonIcon = toggle.querySelector('[data-theme-icon-moon]');

	if (sunIcon) {
		sunIcon.classList.toggle('hidden', isDark);
	}

	if (moonIcon) {
		moonIcon.classList.toggle('hidden', !isDark);
	}
};

const initTheme = () => {
	const stored = localStorage.getItem(themeKey);
	if (stored === 'light' || stored === 'dark') {
		setThemeClass(stored);
		syncThemeToggle();
		return;
	}

	const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
	setThemeClass(prefersDark ? 'dark' : 'light');
	syncThemeToggle();
};

window.toggleTheme = () => {
	const toggle = document.querySelector('[data-theme-toggle]');
	const currentIsDark = document.documentElement.classList.contains('dark');
	const nextIsDark = toggle && toggle.tagName === 'INPUT' && toggle.type === 'checkbox'
		? toggle.checked
		: !currentIsDark;

	localStorage.setItem('theme', nextIsDark ? 'dark' : 'light');
	setThemeClass(nextIsDark ? 'dark' : 'light');
	syncThemeToggle();
};

const optimizeImageLoading = () => {
	const images = document.querySelectorAll('img:not([loading])');

	images.forEach((img) => {
		if (img.dataset.priority === 'high' || img.getAttribute('fetchpriority') === 'high') {
			img.loading = 'eager';
			img.decoding = 'sync';
			return;
		}

		const rect = img.getBoundingClientRect();
		const isNearFold = rect.top <= window.innerHeight * 1.2;
		img.loading = isNearFold ? 'eager' : 'lazy';
		img.decoding = 'async';
	});
};

const initGlobalSubmitLoading = () => {
	const loadingOverlay = document.getElementById('global-submit-loading-overlay');
	const loadingTitle = document.getElementById('global-submit-loading-title');

	if (!loadingOverlay || !loadingTitle) {
		return;
	}

	const resolveRequestMethod = (form) => {
		const formMethod = (form.getAttribute('method') || 'GET').toUpperCase();
		if (formMethod !== 'POST') {
			return formMethod;
		}

		const spoofedMethod = form.querySelector('input[name="_method"]');
		return (spoofedMethod?.value || formMethod).toUpperCase();
	};

	document.addEventListener('submit', (event) => {
		const submittedForm = event.target;
		if (!(submittedForm instanceof HTMLFormElement)) {
			return;
		}

		if (submittedForm.dataset.globalLoading === 'false') {
			return;
		}

		const requestMethod = resolveRequestMethod(submittedForm);
		if (requestMethod === 'GET') {
			return;
		}

		if (submittedForm.dataset.submitting === 'true') {
			event.preventDefault();
			return;
		}

		submittedForm.dataset.submitting = 'true';
		loadingTitle.textContent = submittedForm.dataset.loadingText || 'Saving changes...';
		loadingOverlay.classList.remove('hidden');
		loadingOverlay.classList.add('flex');
		loadingOverlay.setAttribute('aria-hidden', 'false');
		document.body.classList.add('overflow-hidden');

		submittedForm.querySelectorAll('button[type="submit"], input[type="submit"]').forEach((button) => {
			button.disabled = true;
		});
	});
};

document.addEventListener('DOMContentLoaded', () => {
	initTheme();
	optimizeImageLoading();
	initGlobalSubmitLoading();
});

initTheme();
window.addEventListener('DOMContentLoaded', syncThemeToggle);
