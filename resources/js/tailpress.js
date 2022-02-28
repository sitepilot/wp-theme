const theme = (path, theme) => {
    return path.split('.').reduce(function(previous, current) {
        return previous ? previous[current] : null
    }, theme || self);
}

const colorMapper = (colors) => {
    let result = {};

    colors.forEach(function(color) {
        result[''+color.slug.replace('sp-', '')+''] = color.color;
    });

    return result;
}

module.exports = {theme, colorMapper};