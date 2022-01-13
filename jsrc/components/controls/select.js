const { SelectControl } = wp.components;
const { Component } = wp.element;

class Color extends Component {
	static defaultProps = {
		value: "#ffffff",
	};

	constructor() {
		super(...arguments);

		this.state = {
			options: [],
		};
	}

	updateValue(value) {
		this.props.onChange(value);
	}

	componentDidMount() {
		const { localStorage } = window;

		this.setState({ options: JSON.parse(localStorage.getItem("bwpOpts")) });
	}

	render() {
		let { value } = this.props;
		const { options } = this.state;

		return (
			<SelectControl
				value={value}
				options={options}
				onChange={this.updateValue.bind(this)}
			/>
		);
	}
}

export default Color;
