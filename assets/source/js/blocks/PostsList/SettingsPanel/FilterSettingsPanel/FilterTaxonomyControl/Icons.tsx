type Icon = React.FC<{ width?: number; height?: number }>;

export const RadioIcon: Icon = ({ width, height }) => {
	return (
		<svg
			width={width}
			height={height}
			viewBox="0 0 24 24"
			xmlns="http://www.w3.org/2000/svg"
		>
			<title>Radio Button Icon</title>
			<path
				fill-rule="evenodd"
				clip-rule="evenodd"
				d="M12 19.5C16.1421 19.5 19.5 16.1421 19.5 12C19.5 7.85786 16.1421 4.5 12 4.5C7.85786 4.5 4.5 7.85786 4.5 12C4.5 16.1421 7.85786 19.5 12 19.5ZM12 21C16.9706 21 21 16.9706 21 12C21 7.02944 16.9706 3 12 3C7.02944 3 3 7.02944 3 12C3 16.9706 7.02944 21 12 21Z"
			/>
			<circle cx="12" cy="12" r="5.25" />
		</svg>
	);
};

export const MultiSelectIcon: Icon = ({ width, height }) => {
	return (
		<svg
			width={width}
			height={height}
			viewBox="0 0 52 52"
			enable-background="new 0 0 52 52"
			xmlns="http://www.w3.org/2000/svg"
		>
			<title>Multi Select Icon</title>
			<path d="M44,2.5H19c-2.6,0-4.7,2.1-4.7,4.7V8c0,0.5,0.3,0.8,0.8,0.8h22.7c2.6,0,4.7,2.1,4.7,4.7v24.3c0,0.5,0.3,0.8,0.8,0.8H44c2.6,0,4.7-2.1,4.7-4.7V7.2C48.7,4.6,46.6,2.5,44,2.5z" />
			<path d="M33,13.5H8c-2.6,0-4.7,2.1-4.7,4.7v26.6c0,2.6,2.1,4.7,4.7,4.7H33c2.6,0,4.7-2.1,4.7-4.7V18.2C37.8,15.6,35.6,13.5,33,13.5z M31,26.8l-12,12c-0.5,0.5-1,0.7-1.6,0.7c-0.5,0-1.2-0.2-1.6-0.7l-5.8-5.8c-0.5-0.5-0.5-1.2,0-1.6l1.6-1.6c0.5-0.5,1.2-0.5,1.6,0l4.2,4.2l10.3-10.3c0.5-0.5,1.2-0.5,1.6,0l1.6,1.6C31.4,25.6,31.4,26.4,31,26.8z" />
		</svg>
	);
};
