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
		const options = localStorage.getItem("bwpOpts");

		if (options === null) {
			wp.apiFetch({ path: "/wp/v2/pages?per_page=-1" }).then((pages) => {
				if (pages.length > 0) {
					let opts = [];

					pages.map((page) => {
						opts.push({
							label: page.title.rendered,
							value: page.id,
						});
					});

					localStorage.setItem("bwpOpts", JSON.stringify(opts));
					this.setState({ options: opts });
				}
			});
		} else {
			this.setState({ options: JSON.parse(options) });
		}
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
