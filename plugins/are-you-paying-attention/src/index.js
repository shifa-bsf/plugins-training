import "./index.scss"
import { TextControl, Flex, FlexBlock, FlexItem, Button, Icon } from "@wordpress/components"

(function() {
    let locked = false
    
    wp.data.subscribe(function() {
      const results = wp.data.select("core/block-editor").getBlocks().filter(function(block) {
        return block.name == "ourplugin/are-you-paying-attention" && block.attributes.correctAnswer == undefined
      })
      //disable post save button if correct answer is undefined 
      if (results.length && locked == false) {
        locked = true
        wp.data.dispatch("core/editor").lockPostSaving("noanswer")
      }
  
      if (!results.length && locked) {
        locked = false
        wp.data.dispatch("core/editor").unlockPostSaving("noanswer")
      }
    })
})()

wp.blocks.registerBlockType("ourplugin/are-you-paying-attention", {
    title: "Are You Paying Attention?",
    icon: "smiley",
    category: "common",
    attributes: {
        question: { type: "string", default: "" },
        answers: { type: "array", default: [""] },
        correctAnswer: { type: "number", default: undefined }
    },
    edit: EditComponent, // what to show on editor screen
    save: function (props) { // what to show on frontend
        return null
    }
})

function EditComponent(props) {
    const updateQuestion = (value) => {
        props.setAttributes({ question: value })
    }
    const updateAnswer = (value, index) => {
        const newAnswers = props.attributes.answers.concat([])
        newAnswers[index] = value
        props.setAttributes({ answers: newAnswers })
    }

    function deleteAnswer(indexToDelete) {
        const newAnswers = props.attributes.answers.filter(function (x, index) {
            return index != indexToDelete
        })
        props.setAttributes({ answers: newAnswers })

        if (indexToDelete == props.attributes.correctAnswer) {
            props.setAttributes({ correctAnswer: undefined })
        }
    }

    function markAsCorrect(index) {
        props.setAttributes({ correctAnswer: index })
    }

    return (
        <div className="paying-attention-edit-block">
            <TextControl label="Question:" value={props.attributes.question} onChange={updateQuestion} style={{ fontSize: "20px" }} />
            <p style={{ fontSize: "16px", margin: "20px 0 10px 0" }}>Answers:</p>
            {props.attributes.answers
                .map((answer, index) => {
                    return (
                        <Flex>
                            <FlexBlock>
                                <TextControl
                                    value={answer}
                                    autoFocus={answer == undefined}
                                    onChange={(value) => updateAnswer(value, index)}
                                />
                            </FlexBlock>
                            <FlexItem>
                                <Button>
                                    <Icon
                                        className="mark-as-correct"
                                        icon={props.attributes.correctAnswer === index ? "star-filled" : "star-empty"}
                                        onClick={() => markAsCorrect(index)}
                                    />
                                </Button>
                            </FlexItem>
                            <FlexItem>
                                <Button
                                    variant="link"
                                    className="attention-delete"
                                    onClick={() => deleteAnswer(index)}
                                >
                                    Delete
                                </Button>
                            </FlexItem>
                        </Flex>
                    )
                })
            }
            <Button
                variant="primary"
                onClick={() => {
                    props.setAttributes({ answers: props.attributes.answers.concat([""]) })
                }}
            >
                Add another answer
            </Button>
        </div>
    )
}
