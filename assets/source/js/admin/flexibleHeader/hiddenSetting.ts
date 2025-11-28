import { SettingsStorage } from "./interfaces";

class HiddenSetting {
    hiddenField: HTMLElement|null;
    constructor(private hiddenSettingSavedValue: any, private hiddenName: string, private kirkiAttributeName: string) {
        if (this.getHiddenSettingField()) {
            (this.getHiddenSettingField() as HTMLElement).style.display = 'none';
        }
        
        this.hiddenField = null;
    }

    // Returns the hidden settings element or null if cant be found.
    public getHiddenSettingField(): HTMLElement|null {
        if (this.hiddenField) {
            return this.hiddenField;
        }

        this.hiddenField = document.querySelector(`[${this.kirkiAttributeName}="${this.hiddenName}"]`) as HTMLElement|null;
        return this.hiddenField;
    }

    // Sets the value of the hidden field.
    public setHiddenFieldValue(value: SettingsStorage): void {
        wp.customize(this.hiddenName, (setting: any) => {
            setting.set(JSON.stringify(value));
        });
    }

    // Returns the value of the hidden field.
    public getHiddenFieldValue(): SettingsStorage {
        let localHiddenSettingSavedValue = this.hiddenSettingSavedValue;
        if (!localHiddenSettingSavedValue || typeof localHiddenSettingSavedValue !== "string") {
            return {};
        }
  
        try {
            return JSON.parse(localHiddenSettingSavedValue);
        } catch (e) {
            console.error("Could not parse field value: ", e);
        }

        return {};
    }
}

export default HiddenSetting;