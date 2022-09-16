
export function str_replace_array(str, arrayToReplace, replaceTo) {

    arrayToReplace.forEach(toReplace => {
        str = str.replaceAll(toReplace, replaceTo)
    });

    return str

}

export function url_basename(str, sep = '/') {
    return str.substr(str.lastIndexOf(sep) + 1);
}