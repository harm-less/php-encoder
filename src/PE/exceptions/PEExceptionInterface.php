<?php

namespace PE\Exceptions;
/**
 * PEExceptionInterface
 *
 * Exception interface that PE's exceptions should implement
 *
 * This is mostly for having a simple, common Interface class/namespace
 * that can be type-hinted/instance-checked against, therefore making it
 * easier to handle exceptions while still allowing the different
 * exception classes to properly extend the corresponding SPL Exception type
 */
interface PEExceptionInterface
{
}