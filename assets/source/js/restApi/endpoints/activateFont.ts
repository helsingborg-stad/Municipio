import type { ApiCallArgs } from "../newEndpoint";
import { NameSpace, newEndpoint } from "../newEndpoint";

export interface ActivateFontArgs extends ApiCallArgs {
	fontFamily: string;
	fontWeight?: string;
	fontStyle?: string;
	url: string;
}

export interface ActivateFontResponse {
	success: boolean;
	fontFamilyId: number;
}

export const activateFont = newEndpoint<ActivateFontResponse, ActivateFontArgs>(
	{
		nameSpace: NameSpace.MUNICIPIO_V1,
		route: "design-library/activate-font",
		method: "POST",
	},
);
