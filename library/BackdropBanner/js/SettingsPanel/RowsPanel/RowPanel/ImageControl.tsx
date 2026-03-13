import { MediaUpload, MediaUploadCheck } from "@wordpress/block-editor";
import { Button } from "@wordpress/components";
import { __ } from "@wordpress/i18n";

type ImageControlProps = {
	imageId: number;
	imageUrl: string;
	onChange: (imageId: number, imageUrl: string) => void;
};

export const ImageControl: React.FC<ImageControlProps> = ({
	imageId,
	imageUrl,
	onChange,
}) => (
	<div style={{ marginTop: "8px" }}>
		<MediaUploadCheck>
			<MediaUpload
				onSelect={(media: { id: number; url: string }) =>
					onChange(media.id, media.url)
				}
				allowedTypes={["image"]}
				value={imageId}
				render={({ open }) => (
					<div>
						{imageUrl && (
							<img
								src={imageUrl}
								alt=""
								style={{
									width: "100%",
									marginBottom: "8px",
									display: "block",
								}}
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
								onClick={() => onChange(0, "")}
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
