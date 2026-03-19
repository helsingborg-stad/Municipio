import { MediaUpload, MediaUploadCheck } from "@wordpress/block-editor";
import { Button, FocalPointPicker } from "@wordpress/components";
import { __ } from "@wordpress/i18n";

export const ImageControl: React.FC<ImageControlProps> = ({
	imageId,
	imageUrl,
	focalPointX,
	focalPointY,
	onChange,
}) => (
	<div
		className="municipio-backdrop-banner-image-control"
		style={{ marginTop: "8px" }}
	>
		<MediaUploadCheck>
			<MediaUpload
				onSelect={(media: { id: number; url: string }) =>
					onChange(media.id, media.url, 0.5, 0.5)
				}
				allowedTypes={["image"]}
				value={imageId}
				render={({ open }) => (
					<div>
						{imageUrl && (
							<FocalPointPicker
								label={__("Image focal point", "municipio")}
								hideLabelFromVision
								url={imageUrl}
								value={{
									x: focalPointX ?? 0.5,
									y: focalPointY ?? 0.5,
								}}
								onChange={(value) =>
									onChange(imageId, imageUrl, value.x, value.y)
								}
							/>
						)}
						<Button variant="secondary" onClick={open}>
							{imageId
								? __("Change Image", "municipio")
								: __("Select Image", "municipio")}
						</Button>
						{imageId > 0 && (
							<Button
								variant="link"
								isDestructive
								onClick={() => onChange(0, "", 0.5, 0.5)}
								style={{ marginLeft: "8px" }}
							>
								{__("Remove Image", "municipio")}
							</Button>
						)}
					</div>
				)}
			/>
		</MediaUploadCheck>
	</div>
);
