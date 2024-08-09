import { SettingsStorage } from "./interfaces";

class HiddenSetting {
    hiddenField: HTMLElement|null;
    constructor(private hiddenSettingSavedValue: any, private hiddenName: string, private kirkiAttributeName: string) {
        this.hiddenField = null;
    }

    public getHiddenSettingField(): HTMLElement|null {
        if (this.hiddenField) {
            return this.hiddenField;
        }

        this.hiddenField = document.querySelector(`[${this.kirkiAttributeName}="${this.hiddenName}"]`) as HTMLElement|null;
        return this.hiddenField;
    }

    public setHiddenFieldValue(value: any): void {
        wp.customize(this.hiddenName, (setting: any) => {
            setting.set(JSON.stringify(value));
        });
    }

    public getCurrentHiddenFieldValue(): SettingsStorage {
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