import { valueIsHexString } from "./valueIsHexString";

export const scrubHexValue = (input:any):typeof input => {
  if (input) {
    if (typeof input === 'object') {
      for (const [key, value] of Object.entries(input)) {
        if (valueIsHexString(value)) {
          input[key] = value.toLowerCase();
        }
      }
    } else if (valueIsHexString(input)) {
      input = input.toLowerCase();
    }
  }

  return input;
};
