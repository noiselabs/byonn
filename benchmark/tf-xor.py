#!/usr/bin/env python
#
# Adapted from https://gist.github.com/cburgdorf/e2fb46e5ad61ed7b9a29029c5cc30134#file-xor_tensorflow-py

import tensorflow as tf

input_data = [[0., 0.], [0., 1.], [1., 0.], [1., 1.]]
output_data = [[0.], [1.], [1.], [0.]]

n_input = tf.placeholder(tf.float32, shape=[None, 2], name="n_input")
n_output = tf.placeholder(tf.float32, shape=[None, 1], name="n_output")

hidden_nodes = 3

b_hidden = tf.Variable(tf.random_normal([hidden_nodes]), name="hidden_bias")
W_hidden = tf.Variable(tf.random_normal([2, hidden_nodes]), name="hidden_weights")
hidden = tf.sigmoid(tf.matmul(n_input, W_hidden) + b_hidden)

b_output = tf.Variable(tf.random_normal([1]), name="output_bias")
W_output = tf.Variable(tf.random_normal([hidden_nodes, 1]), name="output_weights")
output = tf.sigmoid(tf.matmul(hidden, W_output))

cross_entropy = tf.square(n_output - output)  # simpler, but also works

loss = tf.reduce_mean(cross_entropy)  # mean the cross_entropy
optimizer = tf.train.AdamOptimizer(learning_rate=0.1)
# optimizer = tf.train.GradientDescentOptimizer(learning_rate=0.01)
train = optimizer.minimize(loss)  # let the optimizer train

init = tf.global_variables_initializer()

sess = tf.Session()  # create the session and therefore the graph
sess.run(init)  # initialize all variables

for epoch in xrange(0, 100001):
    # run the training operation
    cvalues = sess.run([train, loss, W_hidden, b_hidden, W_output, b_output],
                       feed_dict={n_input: input_data, n_output: output_data})

    if cvalues[1] < 0.001:
        break

    if epoch % 200 == 0:
        print("")
        print("step: {:>3}".format(epoch))
        print("loss: {}".format(cvalues[1]))

print("W1: {}".format(cvalues[2]))
print("B1: {}".format(cvalues[3]))
print("W2: {}".format(cvalues[4]))
print("B2: {}".format(cvalues[5]))

print("")
print("input: {} | output: {}".format(input_data[0], sess.run(output, feed_dict={n_input: [input_data[0]]})))
print("input: {} | output: {}".format(input_data[1], sess.run(output, feed_dict={n_input: [input_data[1]]})))
print("input: {} | output: {}".format(input_data[2], sess.run(output, feed_dict={n_input: [input_data[2]]})))
print("input: {} | output: {}".format(input_data[3], sess.run(output, feed_dict={n_input: [input_data[3]]})))