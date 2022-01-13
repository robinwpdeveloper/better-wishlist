const { ColorPicker } = wp.components;
const { Component, createRef } = wp.element;

class Color extends Component {
	static defaultProps = {
		value: "#ffffff",
	};

	constructor() {
		super(...arguments);

		this.state = {
			displayPicker: false,
		};

		this.ref = createRef();
	}

	updateValue(color) {
		this.props.onChange(color.hex);
	}

	handleClick = () => {
		this.setState({ displayPicker: !this.state.displayPicker });
	};

	handleClose = (ev) => {
		if (!this.ref.current.contains(ev.target)) {
			this.setState({ displayPicker: false });
		}
	};

	componentDidMount() {
		document.addEventListener("mousedown", this.handleClose);
	}

	componentWillUnmount() {
		document.removeEventListener("mousedown", this.handleClose);
	}

	render() {
		let { value } = this.props;

		return (
			<div className="components-color-picker-wrap" ref={this.ref}>
				<div
					className="components-color-picker-swatch"
					onClick={this.handleClick.bind(this)}
				>
					<div
						className="components-color-picker-preview"
						style={{
							background: value,
						}}
					/>
				</div>
				{this.state.displayPicker && (
					<ColorPicker
						color={value}
						onChangeComplete={this.updateValue.bind(this)}
						disableAlpha
					/>
				)}
			</div>
		);
	}
}

export default Color;
