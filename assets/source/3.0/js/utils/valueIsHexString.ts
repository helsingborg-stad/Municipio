export const valueIsHexString = (value:any):value is string => {
  return typeof value === 'string' && value.indexOf('#') === 0;
};