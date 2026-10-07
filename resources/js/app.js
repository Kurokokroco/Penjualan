import 'bootstrap';

document.querySelectorAll('[data-rupiah-input]').forEach((input) => {
	const formatRupiah = (value) => {
		const digits = value.replace(/\D/g, '');

		return digits === '' ? '' : new Intl.NumberFormat('id-ID').format(Number(digits));
	};

	input.value = formatRupiah(input.value);

	input.addEventListener('focus', () => {
		input.value = input.value.replace(/\./g, '');
	});

	input.addEventListener('blur', () => {
		input.value = formatRupiah(input.value);
	});
});
