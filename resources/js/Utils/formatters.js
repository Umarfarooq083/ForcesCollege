export function formatCNIC(value) {
    value = String(value ?? '');
    const numeric = value.replace(/\D/g, '').slice(0, 13);
    const part1 = numeric.slice(0, 5);
    const part2 = numeric.slice(5, 12);
    const part3 = numeric.slice(12, 13);
    let formatted = part1;
    if (part2) formatted += '-' + part2;
    if (part3) formatted += '-' + part3;
    return formatted;
}


export function formatPhone(value) {
    value = String(value ?? '');
    let numeric = value.replace(/\D/g, '');
    if (!numeric.startsWith('92')) {
        numeric = '92' + numeric.replace(/^92/, '');
    }
    numeric = numeric.slice(0, 12);
    return '+' + numeric;
}