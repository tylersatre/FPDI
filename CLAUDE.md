# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

FPDI (Free PDF Document Importer) is a PHP library that reads pages from existing PDF documents and uses them as templates within PDF generation frameworks (FPDF, TCPDF, tFPDF). Current version: 2.6.4.

## Common Commands

```bash
# Run all tests
composer test

# Run specific test suites
composer test -- --testsuite="Unit Tests"
composer test -- --testsuite="Functional Tests"
composer test -- --testsuite="Visual Tests"

# Run a single test file
composer test -- tests/unit/PdfParser/TokenizerTest.php

# Run a single test method
composer test -- --filter testMethodName

# Code style check (PSR-12)
composer cs

# Static analysis (PHPStan level 5)
composer analyse
```

## Architecture

### Layered Design

```
┌─────────────────────────────────────────────────────────┐
│  Fpdi.php (main entry point)                            │
│  - setSourceFile(), importPage(), useImportedPage()     │
├─────────────────────────────────────────────────────────┤
│  Trait Composition Layer                                │
│  - FpdiTrait: Core PDF import logic                     │
│  - FpdfTplTrait: Template management                    │
│  - FpdfTrait: FPDF integration                          │
├─────────────────────────────────────────────────────────┤
│  PdfReader/ (high-level reading)                        │
│  - PdfReader: Document interface                        │
│  - Page: Individual page with boundary handling         │
├─────────────────────────────────────────────────────────┤
│  PdfParser/ (low-level parsing)                         │
│  - PdfParser: Main parser                               │
│  - Tokenizer: PDF content tokenization                  │
│  - CrossReference/: Xref table parsing                  │
│  - Filter/: Stream decompression (Flate, Ascii85, LZW)  │
│  - Type/: PDF data types (PdfArray, PdfDictionary, etc) │
└─────────────────────────────────────────────────────────┘
```

### Trait-Based Composition

Traits allow FPDI to extend different PDF libraries without multiple inheritance:
- `src/Fpdi.php` - for FPDF
- `src/Tcpdf/Fpdi.php` - for TCPDF
- `src/Tfpdf/Fpdi.php` - for tFPDF

Each uses the same core traits (`FpdiTrait`, `FpdfTplTrait`) but extends different base classes.

### PDF Type System

All PDF primitives are represented as objects in `src/PdfParser/Type/`:
- `PdfType::resolve()` recursively resolves indirect references
- Indirect object references are resolved on-demand (lazy loading)

### Key Classes

| Class | Purpose |
|-------|---------|
| `Fpdi` | Main entry point combining template and import features |
| `PdfReader` | High-level document access, manages page count and retrieval |
| `Page` | Represents a PDF page with boundaries and content stream |
| `PdfParser` | Low-level PDF parsing, tokenization, and object resolution |
| `StreamReader` | Abstraction for reading from files, resources, or strings |

## Namespace

All code uses the `setasign\Fpdi` namespace with PSR-4 autoloading.

## Exception Hierarchy

- `FpdiException` - Base exception
- `PdfParserException` - Parser-level errors
- `CrossReferenceException` - Cross-reference table issues
- `PdfReaderException` - Reader-level issues
- `PdfTypeException` - Type handling errors
- `FilterException` - Stream decompression problems

## Test Structure

- `tests/unit/` - Component-level unit tests
- `tests/functional/` - Integration tests
- `tests/visual/` - Visual regression tests
- `tests/_files/` - Test PDF fixtures
