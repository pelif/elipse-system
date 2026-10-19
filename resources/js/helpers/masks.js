
const formatCnpj = (v) => {
    v = v.replace(/\D/g, '');

    if (v.length >= 3 && v.length < 5)
        v = v.replace(/^(\d{2})(\d{0,2})/, "$1.$2");
    else if (v.length >= 5 && v.length < 9)
        v = v.replace(/^(\d{2})(\d{0,3})/, "$1.$2.");
    else if (v.length >= 9 && v.length < 14)
        v = v.replace(/^(\d{2})(\d{3})(\d{0,3})/, "$1.$2.$3/");
    else if (v.length >= 14 && v.length < 17)
        v = v.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})/, "$1.$2.$3/$4-");
    else if (v.length >= 17)
        v = v.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{0,2})/, "$1.$2.$3/$4-$5");

    return v;
}

const formatCellphone = (v) => {
    v = v.replace(/\D/g, "");
    v = v.replace(/^0/, "");

    if (v.length > 10)
        v = v.replace(/^(\d\d)(\d{5})(\d{4})/, "($1) $2-$3");
    else if (v.length > 5)
        v = v.replace(/^(\d\d)(\d{4})(\d{0,4}).*/, "($1) $2-$3");
    else if (v.length > 2)
        v = v.replace(/^(\d\d)(\d{0,5})/, "($1) $2");
    else
        v = v.replace(/^(\d*)/, "($1");

    return v;
}

/**
 * Ensures a documentation URL string shows an explicit http(s) scheme while typing.
 */
const formatDocumentationUrl = (raw) => {
    let v = String(raw ?? '');
    v = v.trim();
    if (v === '') {
        return '';
    }
    const lower = v.toLowerCase();
    if (lower.startsWith('http://')) {
        return v;
    }
    if (lower.startsWith('https://')) {
        return v;
    }
    let rest = v.replace(/^\/+/, '');
    return 'https://' + rest;
};

/**
 * Normalizes optional documentation URL for submit (empty becomes empty string).
 */
const normalizeDocumentationLinkForSubmit = (raw) => {
    let v = String(raw ?? '');
    v = v.trim();
    if (v === '') {
        return '';
    }
    return formatDocumentationUrl(v);
};

export { formatCnpj, formatCellphone, formatDocumentationUrl, normalizeDocumentationLinkForSubmit };
