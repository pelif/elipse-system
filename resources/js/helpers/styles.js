export const randomBg = () => {
    const bgColors = [
        'bg-blue-400',
        'bg-green-400',
        'bg-red-400',
        'bg-yellow-400',
        'bg-purple-400',
        'bg-pink-400',
        'bg-indigo-400',
        'bg-teal-400',
        'bg-orange-400',
        'bg-gray-400'
    ];
    return bgColors[Math.floor(Math.random() * bgColors.length)];
}

export const printInitials = (name) => {
    let firstName = name.split(' ')[0];

    if (name.split(' ').length > 1) {
        let lastName = name.split(' ')[1];
        return firstName.charAt(0).toUpperCase() + lastName.charAt(0).toUpperCase();
    }
    return firstName.charAt(0).toUpperCase();
}

export function capitalizedText(text) {
    return text.charAt(0).toUpperCase() + text.slice(1);
}
