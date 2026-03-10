import { MediaUpload, MediaUploadCheck } from "@wordpress/block-editor";
import {
	Button,
	PanelBody,
	TextareaControl,
	TextControl,
} from "@wordpress/components";
import { __ } from "@wordpress/i18n";
import type { BackdropBannerEditProps } from "../../Edit";

const emptySlide = (): SlideItem => ({
	id: crypto.randomUUID(),
	title: "",
	description: "",
	url: "",
	imageId: 0,
	imageUrl: "",
});

export const SlidesPanel: React.FC<BackdropBannerEditProps> = ({
	attributes: { slides },
	setAttributes,
}) => {
	const updateSlide = (id: string, updates: Partial<SlideItem>) => {
		const updated = slides.map((slide) =>
			slide.id === id ? { ...slide, ...updates } : slide,
		);
		setAttributes({ slides: updated });
	};

	const addSlide = () => {
		setAttributes({ slides: [...slides, emptySlide()] });
	};

	const removeSlide = (id: string) => {
		setAttributes({ slides: slides.filter((slide) => slide.id !== id) });
	};

	return (
		<PanelBody
			title={__("Backdrop banner settings", "municipio")}
			initialOpen={true}
		>
			{slides.map((slide, index) => (
				<PanelBody
					key={slide.id}
					title={slide.title || `${__("Row", "municipio")} ${index + 1}`}
					initialOpen={false}
					style={{ border: "1px solid #ddd", marginBottom: "8px" }}
				>
					<TextControl
						label={__("Title", "municipio")}
						value={slide.title}
						onChange={(value) => updateSlide(slide.id, { title: value })}
						__nextHasNoMarginBottom
					/>
					<TextareaControl
						label={__("Description", "municipio")}
						value={slide.description}
						onChange={(value) => updateSlide(slide.id, { description: value })}
						__nextHasNoMarginBottom
					/>
					<TextControl
						type="url"
						label={__("URL", "municipio")}
						value={slide.url}
						onChange={(value) => updateSlide(slide.id, { url: value })}
						__nextHasNoMarginBottom
					/>
					<div style={{ marginTop: "8px" }}>
						<MediaUploadCheck>
							<MediaUpload
								onSelect={(media: { id: number; url: string }) =>
									updateSlide(slide.id, {
										imageId: media.id,
										imageUrl: media.url,
									})
								}
								allowedTypes={["image"]}
								value={slide.imageId}
								render={({ open }) => (
									<div>
										{slide.imageUrl && (
											<img
												src={slide.imageUrl}
												alt=""
												style={{
													width: "100%",
													marginBottom: "8px",
													display: "block",
												}}
											/>
										)}
										<Button variant="secondary" onClick={open}>
											{slide.imageId
												? __("Change Image", "municipio")
												: __("Select Image", "municipio")}
										</Button>
										{slide.imageId > 0 && (
											<Button
												variant="link"
												isDestructive
												onClick={() =>
													updateSlide(slide.id, { imageId: 0, imageUrl: "" })
												}
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
					<Button
						variant="link"
						isDestructive
						onClick={() => removeSlide(slide.id)}
						style={{ marginTop: "12px" }}
					>
						{__("Remove row", "municipio")}
					</Button>
				</PanelBody>
			))}
			<Button variant="primary" onClick={addSlide}>
				{__("Add Row", "municipio")}
			</Button>
		</PanelBody>
	);
};
